<?php

namespace Webkul\Admin\Http\Controllers\CMS;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\DataGrids\CMS\CMSPageDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\CMS\Repositories\PageRepository;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected PageRepository $pageRepository) {}

    /**
     * Loads the index page showing the static pages resources.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(CMSPageDataGrid::class)->process();
        }

        return view('admin::cms.index');
    }

    /**
     * To create a new CMS page.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::cms.create');
    }

    /**
     * To store a new CMS page in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'url_key'      => ['required', 'unique:cms_page_translations,url_key', new \Webkul\Core\Rules\Slug],
            'page_title'   => 'required',
            'html_content' => 'required',
        ]);

        Event::dispatch('cms.page.create.before');

        $page = $this->pageRepository->create(request()->only([
            'page_title',
            'channels',
            'html_content',
            'meta_title',
            'url_key',
            'meta_keywords',
            'meta_description',
        ]));

        Event::dispatch('cms.page.create.after', $page);

        session()->flash('success', trans('admin::app.cms.create-success'));

        return redirect()->route('admin.cms.index');
    }

    /**
     * To edit a previously created CMS page.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $page = $this->pageRepository->findOrFail($id);

        return view('admin::cms.edit', compact('page'));
    }

    /**
     * To update the previously created CMS page in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(int $id)
    {
        $locale = core()->getRequestedLocaleCode();

        $this->validate(request(), [
            $locale.'.url_key'      => ['required', new \Webkul\Core\Rules\Slug, function ($attribute, $value, $fail) use ($id) {
                if (! $this->pageRepository->isUrlKeyUnique($id, $value)) {
                    $fail(trans('admin::app.cms.index.already-taken', ['name' => 'Page']));
                }
            }],
            $locale.'.page_title'     => 'required',
            $locale.'.html_content'   => 'required',
        ]);

        Event::dispatch('cms.page.update.before', $id);

        $page = $this->pageRepository->update([
            $locale    => request()->input($locale),
            'channels' => request()->input('channels'),
            'locale'   => $locale,
        ], $id);

        Event::dispatch('cms.page.update.after', $page);

        session()->flash('success', trans('admin::app.cms.update-success'));

        return redirect()->route('admin.cms.index');
    }

    /**
     * To delete the previously create CMS page.
     */
    public function delete(int $id): JsonResponse
    {
        try {
            Event::dispatch('cms.page.delete.before', $id);

            $this->pageRepository->delete($id);

            Event::dispatch('cms.page.delete.after', $id);

        return new JsonResponse(['message' => trans('admin::app.cms.delete-success')]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => trans('admin::app.cms.no-resource')]);
        }
    }

    /**
     * Open the visual page builder.
     */
    public function builder(int $id)
    {
        $page = $this->pageRepository->findOrFail($id);

        return view('admin::cms.builder', compact('page'));
    }

    /**
     * Save builder data via AJAX.
     */
    public function saveBuilderData(int $id): JsonResponse
    {
        $this->validate(request(), [
            'builder_data' => 'required',
            'html_content' => 'required',
        ]);

        try {
            $locale = core()->getRequestedLocaleCode();

            Event::dispatch('cms.page.builder.update.before', $id);

            $page = $this->pageRepository->update([
                'builder_data' => request()->input('builder_data'),
                $locale => [
                    'html_content' => request()->input('html_content'),
                ],
                'locale' => $locale,
            ], $id);

            Event::dispatch('cms.page.builder.update.after', $page);

            return new JsonResponse([
                'message' => trans('admin::app.cms.builder.save-success'),
                'data' => $page
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get builder data for a page.
     */
    public function getBuilderData(int $id): JsonResponse
    {
        try {
            $page = $this->pageRepository->findOrFail($id);

            return new JsonResponse([
                'builder_data' => $page->builder_data ? json_decode($page->builder_data, true) : null,
                'page' => $page
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * To mass delete the CMS resource from storage.
     */
    public function massDelete(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('cms.page.delete.before', $index);

            $this->pageRepository->delete($index);

            Event::dispatch('cms.page.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.cms.index.datagrid.mass-delete-success'),
        ], 200);
    }
}
