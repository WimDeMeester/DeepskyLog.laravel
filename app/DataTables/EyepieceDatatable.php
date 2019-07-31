<?php

/**
 * Eyepiece DataTable.
 *
 * PHP Version 7
 *
 * @category Eyepieces
 * @package  DeepskyLog
 * @author   Wim De Meester <deepskywim@gmail.com>
 * @license  GPL3 <https://opensource.org/licenses/GPL-3.0>
 * @link     http://www.deepskylog.org
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Eyepiece;

/**
 * Eyepiece DataTable.
 *
 * PHP Version 7
 *
 * @category Eyepieces
 * @package  DeepskyLog
 * @author   Wim De Meester <deepskywim@gmail.com>
 * @license  GPL3 <https://opensource.org/licenses/GPL-3.0>
 * @link     http://www.deepskylog.org
 */
class EyepieceDataTable extends DataTable
{
    /**
     * Make the correct ajax call.
     *
     * @return datatables The Correct ajax call.
     */
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn(
                'observername', function ($eyepiece) {
                    return '<a href="/observer/' . $eyepiece->observer_id . '">' . $eyepiece->observer->name . '</a>';
                }
            )->editColumn(
                'name',
                '<a href="/eyepiece/{{ $id }}/edit">{{ $name }}</a>'
            )->editColumn(
                'observations',
                '<a href="/observations/eyepiece/{{ $id }}">{{ $observations }}</a>'
            )->editColumn(
                'active',
                '<form method="POST" action="/eyepiece/{{ $id }}">
                    @method("PATCH")
                    @csrf
                    <input type="checkbox" name="active" onChange="this.form.submit()" {{ $active ? "checked" : "" }}>
                 </form>'
            )->addColumn(
                'delete', '<form method="POST" action="/eyepiece/{{ $id }}">
                            @method("DELETE")
                            @csrf
                            <button type="button" class="btn btn-sm btn-link" onClick="this.form.submit()">
                            <i class="far fa-trash-alt"></i>
                        </button>
                        </form>'
            )->rawColumns(
                ['name', 'observations', 'active', 'delete', 'observername']
            )->make(true);
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        if ($this->user === 'admin') {
            $eyepieces = Eyepiece::select();
        } else {
            $eyepieces = auth()->user()->eyepieces();
        }

        return $this->applyScopes($eyepieces);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        if ($this->user === 'admin') {
            return $this->builder()
                ->columns($this->getColumns())->minifiedAjax()
                ->addColumn(
                    ['data' => 'observername', 'title' => _i('Name'),
                    'name' => 'observername',
                    'orderable' => false,
                    'searchable' => false,
                    ]
                )->parameters($this->getMyParameters());
        } else {
            return $this->builder()
                ->columns($this->getColumns())->minifiedAjax()
                ->parameters($this->getMyParameters());
        }
    }

    /**
     * Returns the parameters and also add the correct translation to the datatables.
     *
     * @return array The parameters
     */
    protected function getMyParameters()
    {
        $language = array("url"=>"http://cdn.datatables.net/plug-ins/1.10.19/i18n/"
            . \PeterColes\Languages\LanguagesFacade::lookup(
                [\Xinax\LaravelGettext\Facades\LaravelGettext::getLocaleLanguage()],
                'en'
            )->first()
            . ".json");
        $mypars = $this->getBuilderParameters();
        $mypars["language"] = $language;
        return $mypars;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        if ($this->user === 'admin') {
            return [
                ['name' => 'name',
                    'title' => _i('Name'),
                    'data' => 'name',
                ],
                ['name' => 'focalLength',
                    'title' => _i('Focal Length'),
                    'data' => 'focalLength',
                    'width' => '10%',
                ],
                ['name' => 'fov',
                    'title' => _i('Apparent Field of View'),
                    'data' => 'apparentFOV',
                    'width' => '10%',
                ],
                ['name' => 'maxFocalLength',
                    'title' => _i('Maximum Focal Length'),
                    'data' => 'maxFocalLength',
                    'width' => '10%',
                ],
                ['name' => 'observations',
                    'title' => _i('Observations'),
                    'data' => 'observations',
                    'width' => '10%',
                ],
                ['name' => 'delete',
                    'title' => _i('Delete'),
                    'data' => 'delete',
                    'orderable' => false,
                    'searchable' => false,
                    'width' => '10%',
                ],
            ];
        } else {
            return [
                ['name' => 'name',
                    'title' => _i('Name'),
                    'data' => 'name',
                ],
                ['name' => 'focalLength',
                    'title' => _i('Focal Length'),
                    'data' => 'focalLength',
                    'width' => '10%',
                ],
                ['name' => 'fov',
                    'title' => _i('Apparent Field of View'),
                    'data' => 'apparentFOV',
                    'width' => '10%',
                ],
                ['name' => 'maxFocalLength',
                    'title' => _i('Maximum Focal Length'),
                    'data' => 'maxFocalLength',
                    'width' => '10%',
                ],
                ['name' => 'observations',
                    'title' => _i('Observations'),
                    'data' => 'observations',
                ],
                ['name' => 'active',
                    'title' => _i('Active'),
                    'data' => 'active',
                ],
                ['name' => 'delete',
                    'title' => _i('Delete'),
                    'data' => 'delete',
                    'orderable' => false,
                    'searchable' => false,
                ],
            ];
        }
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Eyepiece_' . date('YmdHis');
    }
}