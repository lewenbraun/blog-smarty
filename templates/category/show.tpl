{extends file='layouts/base.tpl'}

{block name='title'}{$category->getName()} — Blog Smarty{/block}

{block name='content'}
  <section class="category-page" aria-labelledby="category-title">
    <div class="container">
      <header class="category-page__header">
        <h1 class="category-page__title" id="category-title">{$category->getName()}</h1>
        <p class="category-page__description">{$category->getDescription()}</p>
      </header>

      <nav class="category-sort" aria-label="Article sorting">
        <span class="category-sort__label">Sort by:</span>

        <a
          class="category-sort__link{if $activeSort === 'views'} category-sort__link--active{/if}"
          href="{$viewsSortUrl}"
        >
          Views
          {if $activeSort === 'views'}
            <span class="category-sort__arrow" aria-hidden="true">{if $sortDirection === 'desc'}▼{else}▲{/if}</span>
            <span class="visually-hidden">{if $sortDirection === 'desc'}descending{else}ascending{/if}</span>
          {/if}
        </a>

        <a
          class="category-sort__link{if $activeSort === 'date'} category-sort__link--active{/if}"
          href="{$dateSortUrl}"
        >
          Date
          {if $activeSort === 'date'}
            <span class="category-sort__arrow" aria-hidden="true">{if $sortDirection === 'desc'}▼{else}▲{/if}</span>
            <span class="visually-hidden">{if $sortDirection === 'desc'}descending{else}ascending{/if}</span>
          {/if}
        </a>
      </nav>

      <div class="article-grid">
        {foreach $articles as $article}
          {include file='components/article-card.tpl' article=$article}
        {foreachelse}
          <p class="category-page__empty">No articles in this category yet.</p>
        {/foreach}
      </div>

      {if $totalPages > 1}
        <nav class="category-pagination" aria-label="Pagination">
          {if $previousPageUrl}
            <a class="category-pagination__link" href="{$previousPageUrl}" rel="prev">Previous</a>
          {else}
            <span class="category-pagination__disabled">Previous</span>
          {/if}

          <div class="category-pagination__pages">
            {foreach $pageLinks as $pageLink}
              {if $pageLink.number === $currentPage}
                <span
                  class="category-pagination__number category-pagination__number--current"
                  aria-current="page"
                  aria-label="Page {$pageLink.number}"
                >
                  {$pageLink.number}
                </span>
              {else}
                <a
                  class="category-pagination__link category-pagination__number"
                  href="{$pageLink.url}"
                  aria-label="Page {$pageLink.number}"
                >
                  {$pageLink.number}
                </a>
              {/if}
            {/foreach}
          </div>

          {if $nextPageUrl}
            <a class="category-pagination__link" href="{$nextPageUrl}" rel="next">Next</a>
          {else}
            <span class="category-pagination__disabled">Next</span>
          {/if}
        </nav>
      {/if}
    </div>
  </section>
{/block}
