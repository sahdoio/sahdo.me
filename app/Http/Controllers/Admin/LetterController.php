<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LetterController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function letter($id)
    {
        $member = Member::find($id);
        return view('admin.letter.letter', [
            'page' => 'letter',
            'page_title' => 'Carta de Recomendação',
            'member' => $member
        ]);
    }

    /**
     * @return mixed
     */
    public function export($id)
    {
        $member = Member::find($id);

        if ($member) {
            echo "exportando...";

            $data = [
                'member' => $member
            ];

            $pdf = App::make('dompdf.wrapper');
            $pdf = $pdf->loadView('admin.letter.letter_template', $data);
            $output = 'Carta_Recomendacao_Membro_' . $member->name . '_' . $member->lastaname . '_' . time() . '.pdf';
            return $pdf->download($output);
        }
        else {
            return 'error';
        }
    }

    /**
     * @return mixed
     */
    public function template($id)
    {
        $member = Member::find($id);
        return view('admin.letter.letter_template', [
            'page' => 'letter',
            'page_title' => 'Carta de Recomendação',
            'member' => $member
        ]);
    }
}
