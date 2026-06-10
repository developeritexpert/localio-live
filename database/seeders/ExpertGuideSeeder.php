<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ExpertGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('expert_guides')->truncate();

        DB::table('expert_guides')->insert([
            'lang_id' => 1, // Assuming English is default
            'title' => 'Expert Guides',
            'description' => 'We have a vast knowledge base with articles, guides, how-to, instructions, and answers to our most frequently asked questions.',
            'education_title' => 'Educational Resources You Can Trust.',
            'education_description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
            'smart_search' => 'AI-Powered Smart Search',
            'smart_search_description' => 'Quickly discover and compare the best products with our AI-powered search.',

            // CKEditor Content
            'how_to_check_email' => 'How To Check Email - Webmail & Email Applications.',
            'overview' => 'Overview',
            'email_description' => '<p>Email is one of the most widely used forms of communication...</p>
                                    <p>Once you’ve created your professional email address...</p>
                                    <p>Webmail</p>
                                    <p>Email Applications</p>
                                    <p>IMAP and POP</p>',

            'webmail' => 'Webmail',
            'webmail_description' => '<p>Webmail, or web-based email, is portable and accessible anywhere...</p>
                                      <h6>A few advantages of using webmail:</h6>
                                      <p>Simplicity. No setup is required.</p>
                                      <p>Portable and accessible anywhere.</p>',

            'email_application' => 'Email Applications',
            'email_app_description' => '<p>Email applications are installed software for email management.</p>
                                        <h6>A few advantages of using an email application:</h6>
                                        <p>Accessible offline.</p>
                                        <p>Immediate notifications to your device.</p>',

            'imap' => 'IMAP and POP',
            'imap_pop' => '<p>When an email is sent, the message is stored on an email server...</p>
                           <h6>IMAP</h6>
                           <p>This is the setting you use if you want to access your email on multiple devices.</p>
                           <h6>POP3</h6>
                           <p>If you intend to access your email on only one device, this is the setting for you.</p>',
             'meta_title'=>'Expert Guide Page',
             'meta_description' => 'Here is expert guide page',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
