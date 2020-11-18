<?php namespace App\Controllers;

use App\Paytabs\Category;
use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
	use ResponseTrait;

	public const INITIAL_CATEGORIES = [
		[
			'parent_id' => null,
			'name'      => 'Category A',
		],
		[
			'parent_id' => null,
			'name'      => 'Category B',
		],
	];

	public function index()
	{
		return view('home');
	}

	public function all()
	{
		$categories = $this->categoryModel->getCategories();

		if (empty($categories))
		{
			$this->categoryModel->insertBatch($this->getInitialCategories());

			return $this->all();
		}

		return $this->respond($categories);
	}

	public function select()
	{
		$selectedCategoryId = $this->request->getGet('id');

		if (! $this->validation->setRule('id', 'ID', 'required|belongs_to_me')->run(['id' => $selectedCategoryId])
		)
		{
			return $this->respond($this->validation->getErrors(), 422);
		}

		$category = $this->categoryModel->find($selectedCategoryId);

		if ($this->isRootCategory($category))
		{
			$this->insertMainSubCategories($category);
		}

		if ($this->isFreshSubCategory($category))
		{
			$conventionName = $this->getConventionName($category);

			if ($conventionName === $category->name)
			{
				$this->insertSubCategoriesByConvention($category, $conventionName);
			}
		}

		return $this->respond(['ok' => true]);
	}

	public function add()
	{
		$requestData = (array) $this->request->getJson();

		$isValidRequest = $this->validation
			->setRules([
				'name'      => 'required|min_length[1]|max_length[255]',
				'parent_id' => 'required' . ($requestData['parent_id'] === '0' ? '' : '|belongs_to_me'),
			])
			->run($requestData);

		if (! $isValidRequest)
		{
			return $this->respond($this->validation->getErrors(), 422);
		}

		$this->categoryModel->insert([
			'name'       => $requestData['name'],
			'parent_id'  => $requestData['parent_id'],
			'session_id' => session_id(),
		]);

		return $this->respond(['ok' => true]);
	}

	protected function isRootCategory(Category $category) : bool
	{
		$parents = $this->categoryModel->findParents($category);

		$childs = $this->categoryModel->findChilds($category);

		return empty($childs) && empty($parents) && substr($category->name, 0, 9) === 'Category ' && strlen($category->name) === 10;
	}

	protected function isFreshSubCategory(Category $category) : bool
	{
		$parents = $this->categoryModel->findParents($category);

		$childs = $this->categoryModel->findChilds($category);

		return empty($childs) && ! empty($parents);
	}

	protected function insertMainSubCategories(Category $category) : void
	{
		$this->categoryModel->insertBatch([
			[
				'name'       => 'SUB ' . $category->name[9] . '1',
				'parent_id'  => $category->id,
				'session_id' => session_id(),
			],
			[
				'name'       => 'SUB ' . $category->name[9] . '2',
				'parent_id'  => $category->id,
				'session_id' => session_id(),
			],
		]);
	}

	protected function getConventionName(Category $category) : string
	{
		$parents = $this->categoryModel->findParents($category);

		$conventionName            = '';
		$conventionParentCharacter = null;

		array_walk(
			$parents, function ($parent) use (&$conventionName, &$conventionParentCharacter) {
				if ($parent->parent_id === 0)
				{
					$conventionParentCharacter = $parent->name[9];
				}

				$conventionName .= 'SUB ';
			}
		);

		if (strpos($category->name, $conventionName) === 0
			&& ($conventionNameParts = explode($conventionName . $conventionParentCharacter, $category->name))
			&& (is_numeric(str_replace('-', '', $conventionNameParts[1] ?? '')))
		)
		{
			$conventionName = implode($conventionName . $conventionParentCharacter, $conventionNameParts);
		}

		return $conventionName;
	}

	protected function insertSubCategoriesByConvention(Category $category, string $conventionName) : void
	{
		$this->categoryModel->insertBatch([
			[
				'name'       => 'SUB ' . $conventionName . '-1',
				'parent_id'  => $category->id,
				'session_id' => session_id(),
			],
			[
				'name'       => 'SUB ' . $conventionName . '-2',
				'parent_id'  => $category->id,
				'session_id' => session_id(),
			],
		]);
	}

	protected function getInitialCategories() : array
	{
		return array_map(fn($category) => array_merge($category + ['session_id' => session_id()]), static::INITIAL_CATEGORIES);
	}
}
