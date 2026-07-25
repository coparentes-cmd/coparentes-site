/** Real Polish blog posts. */
export const blogPosts = [
  {
    slug: 'dlaczego-warto-korzystac-z-mediacji-rozwodowej',
    title: 'Dlaczego warto korzystać z mediacji rozwodowej?',
    excerpt:
      'Rozstanie to i tak trudne. Na podstawie konkretnych danych pokażę, dlaczego mediacja naprawdę się opłaca – i to nie tylko finansowo.',
    date: '2026-07-25',
    categories: ['Rozwód', 'Ugoda mediacyjna'],
    imageUrl: '../assets/blog/cover-rozwod-1.svg',
  },
  {
    slug: 'jak-stworzyc-dobra-ugode-pomediacyjna',
    title: 'Jak stworzyć dobrą ugodę pomediacyjną?',
    excerpt:
      'Ugoda mediacyjna to nie tylko formalność – to dokument, który realnie decyduje o Waszej codzienności po rozstaniu. Na co zwrócić uwagę, żeby ugoda była konkretna, realistyczna i faktycznie działała.',
    date: '2026-07-25',
    categories: ['Ugoda mediacyjna', 'Rozwód'],
    imageUrl: '../assets/blog/cover-mediacja-2.svg',
  },
  {
    slug: 'mediacja-okiem-mediatorki',
    title: 'Mediacja okiem mediatorki',
    excerpt:
      'Rozstanie to jeden z trudniejszych momentów w życiu, a mediacja może pomóc przejść przez niego z mniejszą ilością bólu i chaosu. Czym jest mediacja, jak znaleźć mediatora i czego spodziewać się na pierwszym spotkaniu.',
    date: '2026-07-25',
    categories: ['Ugoda mediacyjna', 'Rozwód'],
    imageUrl: '../assets/blog/cover-mediacja-1.svg',
  },
];

export const blogCategories = ['Wszystkie', 'Rozwód', 'Dzieci', 'Ugoda mediacyjna'];

/** Normalize post categories (supports legacy `category` string). */
export function getPostCategories(post) {
  if (Array.isArray(post.categories) && post.categories.length) {
    return post.categories;
  }
  if (typeof post.category === 'string' && post.category) {
    return [post.category];
  }
  return [];
}
