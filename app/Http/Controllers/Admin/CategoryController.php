<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller {

  public function index() {
    return Category::paginate(10);
  }

  public function create() {}

  public function show(Category $category) {}
  public function edit(Category $category) {}
  public function update(Category $category) {}
  public function destroy(Category $category) {}


}
