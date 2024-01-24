<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $categories = Category::query()->get();

            if ($categories->isEmpty()) {
                return response()->json(['error' => 'Nenhuma categoria encontrada'],Response::HTTP_BAD_REQUEST);
            }

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tente Novamente'],Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(CategoryRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $category = new Category($validatedData);
            $category['url'] = Str::slug($validatedData['title']);
            $category->save();

            return response()->json($category,Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tente Novamente!'],Response::HTTP_BAD_REQUEST);
        }
    }

    public function show(string $url): \Illuminate\Http\JsonResponse
    {
        try {
            $category = Category::query()->where('url', $url)->firstOrFail();
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => "Categoria não encontrada, verifique a url: $url e tente novamente"], Response::HTTP_NOT_FOUND);
        }
    }
    public function update(CategoryRequest $request, string $url): \Illuminate\Http\JsonResponse
    {
        try {

            $category = Category::query()->where('url', $url)->firstOrFail();

            if (!empty($category)) {
                $category->update($request->validated());
            }

            $category['url'] = Str::slug($category['title']);
            $category->save();

            return response()->json(['message' => 'Categoria editada com sucesso']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Categoria não encontrada, verifique a url: $url e tente novamente"], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tente Novamente!'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy(string $url): \Illuminate\Http\JsonResponse
    {
        try {
            $category = Category::query()->where('url', $url)->firstOrFail();

            if (!$category->delete()) {
                return response()->json(['error' => 'Tente novamente'], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Categoria não encontrada, verifique a url: $url e tente novamente"], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tente novamente'], Response::HTTP_BAD_REQUEST);
        }
    }
}
