<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Frontend Home Controller
 *
 * Handles homepage, about, and contact pages
 *
 * @author E-commerce Starter Kit
 * @version 1.0.0
 */
class HomeController extends Controller
{
    /**
     * Display the homepage with featured and latest products
     *
     * @method GET
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->limit(8)
            ->get();

        $latestProducts = Product::active()
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('front.home', compact('featuredProducts', 'latestProducts'));
    }

    /**
     * Display the about page
     *
     * @method GET
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('front.about');
    }

    /**
     * Display the contact form or process contact form submission
     *
     * @method GET|POST
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function contact(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10',
            ]);

            $contactEmail = config('settings.contact_email', 'support@example.com');

            try {
                Mail::raw(
                    "Name: {$validated['name']}\nEmail: {$validated['email']}\nSubject: {$validated['subject']}\n\nMessage:\n{$validated['message']}",
                    function ($message) use ($validated, $contactEmail) {
                        $message->to($contactEmail)
                            ->subject('Contact Form: ' . $validated['subject'])
                            ->from($validated['email'], $validated['name']);
                    }
                );

                session()->flash('success', 'Your message has been sent successfully!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to send message. Please try again.');
            }

            return back();
        }

        return view('front.contact');
    }
}