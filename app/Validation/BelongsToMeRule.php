<?php namespace App\Validation;

use App\Models\CategoryModel;

class BelongsToMeRule
{
	public function belongs_to_me(string $id, string &$error = null): bool
	{
		$categoryModel = new CategoryModel;
		$category      = $categoryModel->where('session_id', session_id())->where('id', $id)->asArray()->first();

		if (! $category)
		{
			$error = 'Invalid ID';
			return false;
		}

		return true;
	}
}
