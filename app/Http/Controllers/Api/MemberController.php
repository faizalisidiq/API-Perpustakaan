<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index()
    {
        return Member::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto_member', 'public');
        }

        $member = Member::create($data);

        return response()->json($member, 201);
    }

    public function show($id)
    {
        return Member::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            // hapus foto lama jika ada
            if ($member->foto) {
                Storage::disk('public')->delete($member->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto_member', 'public');
        }

        $member->update($data);

        return response()->json($member);
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        if ($member->foto) {
            Storage::disk('public')->delete($member->foto);
        }

        $member->delete();

        return response()->json(null, 204);
    }
}