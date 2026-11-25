<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RedirectController extends Controller
{
    public function index()
    {
        $title = 'Redirects';
        $breadcrumbs = ['dashboard' => 'Dashboard', 'javascript:void(0);' => 'Redirects'];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();
        $redirects = Redirect::orderByDesc('id')->paginate(25);

        return view('admin.redirects.index', compact('title', 'breadcrumbHtml', 'redirects'));
    }

    public function create()
    {
        $title = 'Add Redirect';
        $breadcrumbs = ['dashboard' => 'Dashboard', 'redirects.index' => 'Redirects', 'javascript:void(0);' => 'Add'];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

        return view('admin.redirects.form', compact('title', 'breadcrumbHtml'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');
        Redirect::create($data);

        return redirect()->route('redirects.index')->with('success', 'Redirect created.');
    }

    public function edit(Redirect $redirect)
    {
        $title = 'Edit Redirect';
        $breadcrumbs = ['dashboard' => 'Dashboard', 'redirects.index' => 'Redirects', 'javascript:void(0);' => 'Edit'];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

        return view('admin.redirects.form', compact('title', 'breadcrumbHtml', 'redirect'));
    }

    public function update(Request $request, Redirect $redirect)
    {
        $data = $this->validatedData($request, $redirect);
        $data['is_active'] = $request->boolean('is_active');
        $redirect->update($data);

        return redirect()->route('redirects.index')->with('success', 'Redirect updated.');
    }

    public function destroy(Redirect $redirect)
    {
        $redirect->delete();

        return redirect()->route('redirects.index')->with('success', 'Redirect deleted.');
    }

    private function validatedData(Request $request, ?Redirect $redirect = null): array
    {
        $data = $request->validate([
            'source_path' => ['required', 'string', 'max:500', 'regex:/^\/(?!\/)[^?#]*$/', Rule::unique('redirects', 'source_path')->ignore($redirect?->id)],
            'destination_url' => ['required', 'string', 'max:2048', function ($attribute, $value, $fail) {
                $isRelativePath = str_starts_with($value, '/') && !str_starts_with($value, '//') && !str_contains($value, '\\');
                $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
                $isSafeAbsoluteUrl = filter_var($value, FILTER_VALIDATE_URL) && in_array($scheme, ['http', 'https'], true);

                if (!$isRelativePath && !$isSafeAbsoluteUrl) {
                    $fail('Destination must be a site path beginning with / or an absolute HTTP(S) URL.');
                }
            }],
            'status_code' => ['required', Rule::in([301, 302, 307, 308])],
        ], [], ['source_path' => 'old path', 'destination_url' => 'destination']);

        $destinationPath = parse_url($data['destination_url'], PHP_URL_PATH) ?: '/';
        $destinationHost = parse_url($data['destination_url'], PHP_URL_HOST);
        if ($destinationPath === $data['source_path'] && (!$destinationHost || $destinationHost === $request->getHost())) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'destination_url' => 'The destination cannot point back to the old path.',
            ]);
        }

        return $data;
    }
}
