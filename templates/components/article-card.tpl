<article class="article-card">
  <a
    class="article-card__media"
    href="/articles/{$article->getSlug()}"
    aria-label="Read {$article->getTitle()}"
  >
    <img
      class="article-card__image"
      src="{$article->getImageUrl()}"
      alt=""
      width="480"
      height="320"
      loading="lazy"
    >
  </a>

  <div class="article-card__content">
    <div class="article-card__meta">
      <time datetime="{$article->getPublishedAt()|date_format:'Y-m-d'}">
        {$article->getPublishedAt()|date_format:'M j, Y'}
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

    <h3 class="article-card__title">
      <a href="/articles/{$article->getSlug()}">{$article->getTitle()}</a>
    </h3>

    <p class="article-card__description">{$article->getDescription()}</p>
  </div>
</article>
