<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SalesController extends Controller
{
    public function index() {
        return Inertia::render('Reports/Sales');
    }

    public function retail() {
        return Inertia::render('Reports/Retail');
    }

    public function ecommerce() {
        return Inertia::render('Reports/Ecommerce');
    }

    public function marketplace() {
        return Inertia::render('Reports/Marketplace');
    }

    public function wholesale() {
        return Inertia::render('Reports/Wholesale');
    }

    public function belsport() {
        return Inertia::render('Reports/Belsport');
    }

    public function bold() {
        return Inertia::render('Reports/Bold');
    }

    public function k1() {
        return Inertia::render('Reports/K1');
    }

    public function drops() {
        return Inertia::render('Reports/Drops');
    }

    public function outlets() {
        return Inertia::render('Reports/Outlets');
    }

    public function locker() {
        return Inertia::render('Reports/Locker');
    }

    public function qsrx() {
        return Inertia::render('Reports/QsRx');
    }

    public function antihuman() {
        return Inertia::render('Reports/Antihuman');
    }

    public function saucony() {
        return Inertia::render('Reports/Saucony');
    }

    public function aufbau() {
        return Inertia::render('Reports/Aufbau');
    }

    public function bamers() {
        return Inertia::render('Reports/Bamers');
    }

    public function crocs() {
        return Inertia::render('Reports/Crocs');
    }

    public function oakley() {
        return Inertia::render('Reports/Oakley');
    }

    public function thelab() {
        return Inertia::render('Reports/TheLab');
    }

    public function hoka() {
        return Inertia::render('Reports/Hoka');
    }

}
