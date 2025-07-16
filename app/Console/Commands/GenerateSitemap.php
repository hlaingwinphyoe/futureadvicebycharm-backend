<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Http;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap for tarotbycharm.com';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Set these values for all URLs
        $lastmod = new \DateTime('2025-07-14');
        $changefreq = 'weekly';
        $priority = 1.0;

        // Static pages
        $sitemap->add(
            Url::create('https://tarotbycharm.com/')
                ->setLastModificationDate($lastmod)
                ->setChangeFrequency($changefreq)
                ->setPriority($priority)
        );
        $sitemap->add(
            Url::create('https://tarotbycharm.com/about')
                ->setLastModificationDate($lastmod)
                ->setChangeFrequency($changefreq)
                ->setPriority($priority)
        );
        $sitemap->add(
            Url::create('https://tarotbycharm.com/contact')
                ->setLastModificationDate($lastmod)
                ->setChangeFrequency($changefreq)
                ->setPriority($priority)
        );
        $sitemap->add(
            Url::create('https://tarotbycharm.com/blogs')
                ->setLastModificationDate($lastmod)
                ->setChangeFrequency($changefreq)
                ->setPriority($priority)
        );
        $sitemap->add(
            Url::create('https://tarotbycharm.com/zodiacs')
                ->setLastModificationDate($lastmod)
                ->setChangeFrequency($changefreq)
                ->setPriority($priority)
        );
        $sitemap->add(
            Url::create('https://tarotbycharm.com/packages')
                ->setLastModificationDate($lastmod)
                ->setChangeFrequency($changefreq)
                ->setPriority($priority)
        );

        // today special post
        $specialResponse = Http::get('https://admin.tarotbycharm.com/api/today-special-post');
        if ($specialResponse->ok()) {
            $special = $specialResponse->json('data');
            if (isset($special['slug'])) {
                $sitemap->add(
                    Url::create('https://tarotbycharm.com/blogs/' . $special['slug'])
                        ->setLastModificationDate($lastmod)
                        ->setChangeFrequency($changefreq)
                        ->setPriority($priority)
                );
            }
        }

        // Blog posts
        $response = Http::get('https://admin.tarotbycharm.com/api/all-posts');
        if ($response->ok()) {
            $posts = $response->json('data');
            foreach ($posts as $post) {
                if (isset($post['slug'])) {
                    $lastmodDate = isset($post['updated_at']) ? new \DateTime($post['updated_at']) : $lastmod;
                    $sitemap->add(
                        Url::create('https://tarotbycharm.com/blogs/' . $post['slug'])
                            ->setLastModificationDate($lastmodDate)
                            ->setChangeFrequency($changefreq)
                            ->setPriority($priority)
                    );
                }
            }
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
