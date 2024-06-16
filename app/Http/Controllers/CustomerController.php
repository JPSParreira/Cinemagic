<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerFormRequest;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Customer::class);
    }

    public function index(Request $request): View
    {
        $filterByName = $request->search;
        $filterByPayment = $request->payment_type;

        $customersQuery = Customer::query()
            ->join('users', 'customers.id', '=', 'users.id')
            ->withExists('user');

        if ($filterByName !== null) {
            $customersQuery->where(function ($query) use ($filterByName) {
                $query
                    ->where('users.name', 'LIKE', '%' . $filterByName . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $filterByName . '%')
                    ->orWhere('nif', 'LIKE', '%' . $filterByName . '%');
            });
        }

        if ($filterByPayment !== null) {
            $customersQuery->where('payment_type', $filterByPayment);
        }

        $customers = $customersQuery
            ->orderBy('users.name')
            ->with('user')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'filterByName', 'filterByPayment'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', ['customer' => $customer]);
    }

    public function show(Customer $customer)
    {
        return view('customers.show', ['customer' => $customer]);
    }

    public function block(Customer $customer): RedirectResponse
    {
        $user = $customer->user;
        if ($user->blocked == 1) {
            $user->update(['blocked' => 0]);
            return redirect()->route('customers.index')
                ->with('alert-type', 'success')
                ->with('alert-msg', "Customer <u>$user->name</u> has been unblocked successfully!");
        } else {
            $user->update(['blocked' => 1]);
            return redirect()->route('customers.index')
                ->with('alert-type', 'success')
                ->with('alert-msg', "Customer <u>$user->name</u> has been blocked successfully!");
        }
    }

    public function update(CustomerFormRequest $request, Customer $customer)
    {
        $user = $customer->user;

        $validated = $request->validated();

        $customer->update([
            'nif' => $validated['nif'],
            'payment_type' => $validated['payment_type'],
            'payment_ref' => $validated['payment_ref'],
        ]);

        if ($user->email != $request->email) {
            $validated = $request->validate(['email' => 'unique:users,email']);
            $user->update(
                ['email' => $validated['email']]);
        }
        if ($request->has('name')) {
            $user->update([
                'name' => $validated['name'],
            ]);
        }

        if ($request->hasFile('photo_filename')) {
            if ($user->photo_filename &&
                Storage::fileExists('public/photos/' . $user->photo_filename)) {
                Storage::delete('public/photos/' . $user->photo_filename);
            }
            $path = $request->photo_filename->store('public/photos');
            $user->photo_filename = basename($path);
            $user->save();
        }

        return redirect()->route('customers.show', ['customer' => $customer])
            ->with('alert-msg', 'Customer "' . $user->name . '" has been updated successfully!')
            ->with('alert-type', 'success');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $user = $customer->user;
        $customer->delete();
        $user->delete();
        return redirect()->route('customers.index')
            ->with('alert-msg', 'Customer "' . $user->name . '" has been deleted successfully!')
            ->with('alert-type', 'success');
    }


//    public function update(UserFormRequest $request, User $user)
//    {
//        //TODO
//        $user->update($request->validated());
//        $user->save();
//        return redirect()->route('user.edit', ['user' => $user])
//            ->with('alert-msg', 'User "' . $user->name . '" foi alterado com sucesso!')
//            ->with('alert-type', 'success');
//    }

}

/*
 *
    //Dados clientes update
    public function cliente_update(Request $request)
   {
      $user = auth()->user();
      $random = Str::random(10);
      //dd(strlen($request->nif));
      //valida se houver alterações
      if ($request->name == $user->name and $request->nif == $user->Cliente->nif and $request->tipo_pagamento == $user->Cliente->tipo_pagamento and $request->foto_url == null) {
         return back()
                ->with('alert-msg', 'Sem dados a alterar')
                ->with('alert-type', 'success');
      }


      //update nome
      if($request->name){
         $validatedData = $request->validate(['name' => 'required|max:50']);
         User::where('id', $user->id)->update(['name' => $validatedData['name']]);
      }
      //update nif
      if($request->nif){
         if (strlen($request->nif) == 9) {
         $validatedData = $request->validate(['nif' => 'required']);
         Cliente::where('id', $user->id)->update(['nif' => $validatedData['nif']]);
      }else{
         return back()
             ->with('alert-msg', 'NIF invalido')
             ->with('alert-type', 'success');
      }
      }
      //update tipo_pagamento
      if($request->tipo_pagamento){
         if ($request->tipo_pagamento != 'NENHUM') {
            Cliente::where('id', $user->id)->update(['tipo_pagamento' => $request->tipo_pagamento]);
         }
      }
      //update foto
      if($request->foto_url){
         $nameFile = ($user->id . '_'. $random . '.' . $request->foto_url->extension());
         $request->foto_url->move(public_path('storage/fotos'), $nameFile);
         User::where('id', $user->id)
              ->update(['foto_url' => $nameFile]);
      }




        return back()
            ->with('alert-msg', 'Dados atualizados com sucesso!')
            ->with('alert-type', 'success');
    }
 */
