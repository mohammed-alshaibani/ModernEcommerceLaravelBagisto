<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\CMS\PageController;

/**
 * CMS routes.
 */
Route::controller(PageController::class)->prefix('cms')->group(function () {
    Route::get('/', 'index')->name('admin.cms.index');

    Route::get('create', 'create')->name('admin.cms.create');

    Route::post('create', 'store')->name('admin.cms.store');

    Route::get('edit/{id}', 'edit')->name('admin.cms.edit');

    Route::put('edit/{id}', 'update')->name('admin.cms.update');

    Route::delete('edit/{id}', 'delete')->name('admin.cms.delete');

    /**
     * Page Builder routes.
     */
    Route::get('builder/{id}', 'builder')->name('admin.cms.builder');

    Route::post('builder/{id}/save', 'saveBuilderData')->name('admin.cms.builder.save');

    Route::get('builder/{id}/data', 'getBuilderData')->name('admin.cms.builder.data');

    Route::post('mass-delete', 'massDelete')->name('admin.cms.mass_delete');
});
