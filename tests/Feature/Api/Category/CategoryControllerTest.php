<?php

namespace Tests\Feature\Api\Category;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{

    public function testIndexReturnsCategories(): void
    {
        Category::factory()->count(3)->create();
        $response = $this->get('/api/categories');

        // Cenário 1: Categorias existentes
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'url',
                'description',
                'created_at',
                'updated_at',
            ],
        ]);

        $responseData = $response->json();
        $this->assertCount(3, $responseData);

        // Cenário 2: rota não encontrada
        $responseError = $this->get('/api/categories/nonexistent-url');
        $responseError->assertStatus(Response::HTTP_NOT_FOUND);

        // Cenário 3: Categorias vazias
        Category::query()->delete();

        $responseEmpty = $this->get('/api/categories');
        $responseEmpty->assertStatus(Response::HTTP_BAD_REQUEST);
        $responseEmpty->assertJson(['error' => 'Nenhuma categoria encontrada']);
    }

    public function testStoreReturnsCategories(): void
    {
        // Cenário de sucesso ao criar uma nova categoria
        $response = $this->postJson('/api/category', [
            $title = 'title' => 'Nova Categoria',
            'url' => Str::slug($title),
            'description' => 'Descrição da nova categoria',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'id',
            'title',
            'url',
            'description',
            'created_at',
            'updated_at',
        ]);

        // Cenário de erro ao fornecer titulo inválido
        $responseInvalidData = $this->postJson('/api/category', [
            'title' => '',
            'description' => 'Descrição inválida',
        ]);

        $responseInvalidData->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseInvalidData->assertJson([
            "message" => "The title field is required.",
            'errors' => [
                'title' => ['The title field is required.'],
            ],
        ]);

        // Cenário de erro ao fornecer descrição inválida
        $responseInvalidData = $this->postJson('/api/category', [
            'title' => 'Título inváldo',
            'description' => '',
        ]);

        $responseInvalidData->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseInvalidData->assertJson([
            "message" => "The description field is required.",
            'errors' => [
                'description' => ['The description field is required.'],
            ],
        ]);

        // Cenário de erro ao fornecer descrição e titulo inválidos
        $responseInvalidData = $this->postJson('/api/category', [
            'title' => '',
            'description' => '',
        ]);

        $responseInvalidData->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseInvalidData->assertJson([
            "message" => "The title field is required. (and 1 more error)",
            'errors' => [
                "title" => ["The title field is required."],
                'description' => ['The description field is required.'],
            ],
        ]);

        // Cenário de erro quando a descrição e o titulo já foram utilizados
        $responseInvalidData = $this->postJson('/api/category', [
            'title' => 'Nova Categoria',
            'description' => 'Descrição da nova categoria',
        ]);

        $responseInvalidData->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseInvalidData->assertJson([
            "message" => "The title has already been taken. (and 1 more error)",
            'errors' => [
                "title" => ["The title has already been taken."],
                'description' => ['The description has already been taken.'],
            ],
        ]);

        // Cenário 2: rota não encontrada
        $responseError = $this->postJson('/api/categoryy',[
            'title' => 'Titulo inexistente',
            'description' => 'Descrição inexistente',
        ]);

        $responseError->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowReturnsCategories(): void
    {
        // Cenário 1: Categoria existente
        $category = Category::factory()->create();
        $response = $this->get("/api/category/{$category->url}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($category->toArray());

        // Cenário 2: Categoria não encontrada
        $responseNotFound = $this->get('/api/category/nonexistent-url');
        $responseNotFound->assertStatus(Response::HTTP_NOT_FOUND);
        $responseNotFound->assertJson(['error' => 'Categoria não encontrada, verifique a url: nonexistent-url e tente novamente']);
    }

    public function testUpdateReturnsCategories(): void
    {
        // Cenário 1: Categoria existente, atualização bem-sucedida
        $category = Category::factory()->create([
            $title = 'title' => 'Nova Categoria',
            'url' => Str::slug($title),
            'description' => 'Descrição da nova categoria'
        ]);

        $updatedData = [
            $updatedTitle = 'title' => 'Título Atualizado',
            'url' => Str::slug($updatedTitle),
            'description' => 'Descrição Atualizada',
        ];

        $response = $this->put("/api/category/{$category->url}", $updatedData);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['message' => 'Categoria editada com sucesso']);

        // Recarrega a categoria após a atualização e verifica se os dados foram alterados
        $category->refresh();
        $this->assertEquals('Título Atualizado', $category->title);
        $this->assertEquals('titulo-atualizado', $category->url);
        $this->assertEquals('Descrição Atualizada', $category->description);

        // Cenário 2: Categoria não encontrada
        $responseNotFound = $this->put("/api/category/{$category->url}.s", [
            $updatedTitle = 'title' => 'Título Atualizados',
            'url' => Str::slug($updatedTitle),
            'description' => 'Descrição Atualizadas',
        ]);

        $responseNotFound->assertStatus(Response::HTTP_NOT_FOUND);
        $responseNotFound->assertJson(['error' => "Categoria não encontrada, verifique a url: $category->url.s e tente novamente"]);

        // Cenário de erro ao não fornecer titulo
        $responseInvalidData = $this->putJson("/api/category/{$category->url}", [
            'title' => '',
            'description' => 'Descrição Invalida',
        ]);

        $responseInvalidData->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseInvalidData->assertJson([
            "message" => "The title field is required.",
            'errors' => [
                'title' => ['The title field is required.'],
            ],
        ]);

        // Cenário de erro ao não fornecer descrição
        $responseInvalidData = $this->putJson("/api/category/{$category->url}", [
            'title' => 'Titulo',
            'description' => '',
        ]);

        $responseInvalidData->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseInvalidData->assertJson([
            "message" => "The description field is required.",
            'errors' => [
                'description' => ['The description field is required.'],
            ],
        ]);

        // Cenário de erro ao não fornecer descrição e titulo
        $responseInvalidData = $this->putJson("/api/category/{$category->url}", []);

        $responseInvalidData->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseInvalidData->assertJson([
            "message" => "The title field is required. (and 1 more error)",
            'errors' => [
                "title" => ["The title field is required."],
                'description' => ['The description field is required.'],
            ],
        ]);

        // Cenário de erro ao fornecer descrição e titulo no formato errado
        $responseInvalidData = $this->putJson("/api/category/{$category->url}", [
            'title' => 1111111111111,
            'description' => 2222222222222222,
        ]);

        $responseInvalidData->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseInvalidData->assertJson([
            "message" => "The title field must be a string. (and 1 more error)",
            'errors' => [
                "title" => ["The title field must be a string."],
                'description' => ['The description field must be a string.'],
            ],
        ]);
    }

    public function testDeleteReturnsCategories(): void
    {
        // Cenário 1: Exclusão Bem-Sucedida
        $category = Category::factory()->create();
        $response = $this->deleteJson("/api/category/{$category->url}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        // Cenário 2: Categoria Não Encontrada
        $response = $this->deleteJson('/api/category/url_que_nao_existe');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['error' => 'Categoria não encontrada, verifique a url: url_que_nao_existe e tente novamente']);
    }

}
