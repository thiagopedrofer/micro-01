<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $categories = Category::query()->get();

        return response()->json($categories);
    }

    public function store(CategoryRequest $request): \Illuminate\Http\JsonResponse
    {
        $category = new Category($request->validated());
        $category['url'] = Str::slug($category['title']);
        $category->save();

        return response()->json($category);
    }

    public function show(string $url): \Illuminate\Http\JsonResponse
    {
        try {
            $category = Category::query()->where('url', $url)->firstOrFail();
            return response()->json($category);
        } catch (ModelNotFoundException) {
            return response()->json([
                'error' => "Categoria não encontrada, verifique a url: $url e tente novamente"],404);
        }
    }
    public function update(CategoryRequest $request, string $url): \Illuminate\Http\JsonResponse
    {
        $category = Category::query()->where('url', $url)->firstOrFail();

        $category->update($request->all());

        $category['url'] = Str::slug($category['title']);
        $category->save();

        return response()->json(['message' => 'Categoria editada com sucesso']);
    }
    public function destroy(string $url): \Illuminate\Http\JsonResponse
    {
        $category = Category::query()->where('url', $url)->firstOrFail();

        $category->delete();

        return response()->json([],'204');
    }
}
