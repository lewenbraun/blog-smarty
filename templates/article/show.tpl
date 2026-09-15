{extends file='layouts/base.tpl'}

{block name='title'}{$article->getTitle()} — Blog Smarty{/block}

{block name='content'}
  <article class="article">
    <header class="article__header container container--narrow">
      <h1 class="article__title">{$article->getTitle()}</h1>
      <p class="article__description">{$article->getDescription()}</p>
    </header>

    <figure class="article__media container">
      <img
        class="article__image"
        src="{$article->getImageUrl()}"
        alt=""
        width="960"
        height="640"
      >
    </figure>

    <div class="article__body container container--content">
      {$article->getContent()}
    </div>

    <footer class="article__footer container container--content">
      <ul class="article__categories" aria-label="Article categories">
        {foreach $article->getCategories() as $category}
          <li class="article__category">{$category->getName()}</li>
        {/foreach}
      </ul>

      <div class="article__footer-meta">
        <time datetime="{$article->getPublishedAt()|date_format:'Y-m-d'}">
          {$article->getPublishedAt()|date_format:'F j, Y'}
        </time>

        <span class="view-count" aria-label="{$article->getViews()} views">
          <svg
            class="view-count__icon"
            viewBox="0 0 24 24"
            aria-hidden="true"
            focusable="false"
          >
            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
            <circle cx="12" cy="12" r="2.75"/>
          </svg>
          {$article->getViews()}
        </span>
      </div>
    </footer>
  </article>

  {if $similarArticles}
    <section class="related-articles" aria-labelledby="related-articles-title">
      <div class="container">
        <header class="section-header">
          <h2 id="related-articles-title" class="section-header__title">Similar articles</h2>
        </header>

        <div class="article-grid">
          {foreach $similarArticles as $similarArticle}
            {include file='components/article-card.tpl' article=$similarArticle}
          {/foreach}
        </div>
      </div>
    </section>
  {/if}
{/block}
