<?php

namespace App\Http\Controllers;

use App\Livewire\Event\All;
use App\Livewire\Ticket\Index;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->role->name;

        if ($user == 'user'){
            return redirect()->route('tickets');
        }else{
            return redirect()->route('events');
        }
    }
}
