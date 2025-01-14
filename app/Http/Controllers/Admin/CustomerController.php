<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $customers = User::query()
            ->customer()
            ->filterOn()
            ->latest()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'disabled' => $customer->disabled,
                'profile' => $customer->profile
            ]);

        return Inertia::render('Admin/Customer/Index', [
            'customers' => $customers
        ]);
    }

    public function show(User $customer)
    {
        return Inertia::render('Admin/Customer/Show', [
            'customer' => $customer
        ]);
    }

    public function toggleBan(User $customer)
    {
        $data = DB::transaction(function () use ($customer) {
            if ($customer->disabled === 0) {
                $customer->update(['disabled' => 1]);
            } else {
                $customer->update(['disabled' => 0]);
            }
        });

        return redirect()->back()->with('success', 'Successfully Updated.');
    }
}
