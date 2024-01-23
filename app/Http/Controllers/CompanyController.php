<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{

    public function index(): \Illuminate\Http\JsonResponse
    {
        $companies = Company::query()->with('category')->get();

        return response()->json($companies);
    }

    public function store(CompanyRequest $request): \Illuminate\Http\JsonResponse
    {
        $company = Company::query()->create([
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'url' => Str::slug($request->input('name')),
            'uuid' => Str::uuid(),
            'whatsapp' => $request->input('whatsapp'),
            'email' => $request->input('email'),
            'facebook' => $request->input('facebook'),
            'phone' => $request->input('phone'),
            'instagram' => $request->input('instagram'),
            'youtube' => $request->input('youtube')
        ]);

        if (!empty($company->category)) {
            $company->category = $company->category;
        }
        unset($company['category_id']);

        return response()->json($company);

    }
    public function show(string $uuid, Company $company): \Illuminate\Http\JsonResponse
    {
        try {
            $company = Company::query()->where('uuid', $uuid)->with('category')->firstOrFail();
            return response()->json($company);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => "Empresa não encontrada, verifique o identificador: $uuid e tente novamente"
            ], 404);
        }
    }
    public function update(CompanyRequest $request, string $uuid): \Illuminate\Http\JsonResponse
    {
        try {
            $company = Company::query()->where('uuid', $uuid)->with('category')->firstOrFail();

            $company->update($request->validated());

            $company['url'] = Str::slug($company['name']);
            $company->save();

            return response()->json(['message' => 'Empresa atualizada com sucesso']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Empresa não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno no servidor'], 500);
        }
    }
    public function destroy(string $uuid): \Illuminate\Http\JsonResponse
    {
        if (!Str::isUuid($uuid)) {
            return response()->json(['error' => 'Formato de UUID inválido'], 400);
        }

        try {
            $company = Company::query()->where('uuid', $uuid)->with('category')->firstOrFail();

            if (!$company->delete()) {
                return response()->json(['error' => 'Falha ao excluir a empresa'], 500);
            }

            return response()->json([], 204);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Empresa não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno no servidor'], 500);
        }
    }
    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Company::query();

        $query->when($request->filled('name'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->input('name') . '%');
        });

        $query->when($request->filled('email'), function ($q) use ($request) {
            $q->where('email', 'like', '%' . $request->input('email') . '%');
        });

        $query->when($request->filled('phone'), function ($q) use ($request) {
            $q->where('phone', 'like', '%' . $request->input('phone') . '%');
        });

        $companies = $query->get();

        return response()->json($companies);
    }


}
