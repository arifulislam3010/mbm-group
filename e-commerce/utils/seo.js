export const createSEOMeta = (data) => [
    { hid: 'description', name: 'description', content: data.description || '' },
    { hid: 'keywords', name: 'keywords', content: data.keywords || '' },
    { hid: 'image', property: 'image', content: data.image || '' },
    { hid: 'url', property: 'url', content: data.url },
    { hid: 'og:title', property: 'og:title', content: data.title },
    { hid: 'og:description', property: 'og:description', content: data.description || '' },
    { hid: 'og:image', property: 'og:image', content: data.image || '' },
    { hid: 'og:url', property: 'og:url', content: data.url },
    { hid: 'twitter:card', name: 'twitter:card', content: data.cardType || 'summary_large_image'}
]