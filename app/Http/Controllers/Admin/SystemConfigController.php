<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemConfigController extends \App\Http\Controllers\Controller
{
    public function index(): View
    {
        $configs = SystemConfig::orderBy('group_name')->orderBy('sort_order')->paginate(20);
        $groups = SystemConfig::distinct()->select('group_name')->pluck('group_name');
        return view('admin.system-config.index', compact('configs', 'groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'key' => 'required|string|max:100|unique:system_configs,key',
            'value' => 'nullable|string',
            'type' => 'required|in:string,integer,float,boolean,json',
            'group_name' => 'required|string|max:50',
            'label' => 'nullable|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        SystemConfig::create($data);
        SystemConfig::clearCache();
        return back()->with('success', 'Config added');
    }

    public function update(Request $request, SystemConfig $systemConfig): RedirectResponse
    {
        $data = $request->validate([
            'value' => 'nullable|string',
            'type' => 'required|in:string,integer,float,boolean,json',
            'group_name' => 'required|string|max:50',
            'label' => 'nullable|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'boolean',
        ]);

        $systemConfig->update($data);
        SystemConfig::clearCache();
        return back()->with('success', 'Config updated');
    }

    public function destroy(SystemConfig $systemConfig): RedirectResponse
    {
        $systemConfig->delete();
        SystemConfig::clearCache();
        return back()->with('success', 'Config deleted');
    }
}
