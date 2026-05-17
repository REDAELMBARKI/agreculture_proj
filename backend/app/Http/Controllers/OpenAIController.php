<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OpenAIController extends Controller
{
    public function ask(Request $request)
    {
        // validate input
        $request->validate([
            'question' => 'required|string',
        ]);

        $question = $request->input('question');

        // checks if API key is set
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return back()->with('error', 'API key is missing.');
        }

        try {
            $client = \OpenAI::client($apiKey);

            $faqContext = "
            FAQ for Kids Marketplace:
            1. How do I sell an item?
            - Create an account.
            - Click on 'Add Announcement'.
            - Fill in the details and upload images.

            2. What can I sell?
            - Toys, clothes, books, and anything related to kids.

            3. How do I contact a seller?
            - Go to the product page.
            - Use the chat feature or call the provided phone number.

            4. Is it free to list?
            - Yes, listing items is completely free.
            ";

            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo-0125',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a FAQs bot for a Kids Marketplace.'],
                    ['role' => 'system', 'content' => $faqContext],
                    ['role' => 'user', 'content' => $question],
                ],
            ]);

            $answer = $response->choices[0]->message->content ?? 
                      "Sorry, I could not generate a response.";

            return view('faq.answer', compact('answer'));

        } catch (\OpenAI\Exceptions\RateLimitException $e) {
            Log::warning('Openai rate limit exceeded: '.$e->getMessage());
            return back()->with('error', 'Please try again later.');
        
        } catch (\Exception $e) {
            Log::error('Openai general error: '.$e->getMessage());
            return back()->with('error', 'Service error: '.$e->getMessage());
        }
    }
}
