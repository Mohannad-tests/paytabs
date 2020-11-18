<?php namespace App\Models;

use CodeIgniter\Model;
use App\Paytabs\Category;

class CategoryModel extends Model
{
	protected $table      = 'categories';
	protected $primaryKey = 'id';

	protected $returnType = Category::class;

	protected $allowedFields = [
		'id',
		'parent_id',
		'name',
		'session_id',
	];

	protected string $sessionId;

	/*
	* General Queries
	*/
	public function getCategories() : array
	{
		return $this->where(['session_id' => $this->sessionId])
			->findAll();
	}

	/*
	* Single Category Queries
	*/
	public function findParents(Category $category) : array
	{
		$parents = [];

		$categories = $this->getCategories($this->sessionId);

		$this->setParentsRecursively($category, $categories, $parents);

		return $parents;
	}

	public function findSiblings(Category $category) : array
	{
		return $this->whereNotIn('id', [$category->id])->where('parent_id', $category->parent_id)->findAll();
	}

	public function findChilds(Category $category) : array
	{
		return $this->where('parent_id', $category->id)->findAll();
	}

	/*
	* Helper functions
	*/
	protected function setParentsRecursively(Category $searchCategory, array $categories, array &$parents) : void
	{
		$parent = array_values(array_filter($categories, fn ($category) => $category->id === $searchCategory->parent_id));

		if (! empty($parent))
		{
			array_push($parents, $parent[0]);

			$this->setParentsRecursively($parent[0], $categories, $parents);
		}
	}

	public function setSessionId(string $sessionId) : void
	{
		$this->sessionId = $sessionId;
	}
}
