<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['user_id'] = Auth::id();

        Store::create($data);

        return redirect()->route('dashboard')->with('status', 'Tạo cửa hàng thành công.');
    }
}
