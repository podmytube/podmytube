<?php

declare(strict_types=1);

namespace App\Sitemap;

use Illuminate\Support\Collection;

class Sitemap
{
    protected Collection $sitemapNodes;

    private function __construct()
    {
        $this->sitemapNodes = collect([]);
    }

    public static function init(...$params): static
    {
        return new static(...$params);
    }

    public function addNode(SitemapNode $sitemapNode): static
    {
        $this->sitemapNodes->push($sitemapNode);

        return $this;
    }

    public function addNodes(Collection $sitemapNodes): static
    {
        $this->sitemapNodes = $this->sitemapNodes->merge($sitemapNodes);

        return $this;
    }

    public function render(): string
    {
        return view('sitemap.main')
            ->with([
                'nodes' => $this->sitemapNodes,
            ])
            ->render()
        ;
    }

    public function save(): void
    {
        file_put_contents(public_path('sitemap.xml'), $this->render());
    }
}
