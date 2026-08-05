/** Real Polish blog posts. */
export const blogPosts = [
  {
    slug: 'dlaczego-warto-korzystac-z-mediacji-rozwodowej',
    title: 'Dlaczego warto korzystać z mediacji rozwodowej?',
    excerpt:
      'Rozstanie to i tak jedna z trudniejszych rzeczy, jakie Was w życiu spotkały. Smutek miesza się ze złością, ulga z poczuciem winy, a do tego dochodzi strach o dzieci, o pieniądze, o to, co będzie dalej.',
    date: '2026-07-25',
    categories: ['Rozwód', 'Ugoda mediacyjna'],
    imageUrl: '../assets/blog/cover-rozwod-1.svg',
  },
  {
    slug: 'jak-stworzyc-dobra-ugode-pomediacyjna',
    title: 'Jak stworzyć dobrą ugodę pomediacyjną?',
    excerpt:
      'Dotarliście do etapu, w którym uda Wam się usiąść razem, porozmawiać i dojść do porozumienia. To ogromny krok.',
    date: '2026-07-25',
    categories: ['Ugoda mediacyjna', 'Rozwód'],
    imageUrl: '../assets/blog/cover-mediacja-2.svg',
  },
  {
    slug: 'mediacja-okiem-mediatorki',
    title: 'Mediacja okiem mediatorki',
    excerpt:
      'Mediacja to dobrowolny, poufny sposób rozwiązywania sporów przy pomocy niezależnej, bezstronnej osoby – mediatora. Mediacja pozwala zwaśnionym stronom dojść do podłoża problemu.',
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
