import { defineUserConfig } from 'vuepress';
import { defaultTheme } from '@vuepress/theme-default';
import { searchPlugin } from '@vuepress/plugin-search';
import { viteBundler } from '@vuepress/bundler-vite';

export default defineUserConfig({
  lang: 'en-US',
  title: 'Livewire Media Selector',
  description:
    'Documentation for the Livewire-based media selector with performance-oriented best practices.',
  dest: '../docs',
  theme: defaultTheme({
    repo: 'drpshtiwan/livewire-media-selector',
    docsDir: 'docs-src',
    lastUpdatedText: 'Updated on',
    navbar: [
      { text: 'Guide', link: '/guide/getting-started.html' },
      { text: 'Configuration', link: '/guide/configuration.html' },
      {
        text: 'Livewire Integration',
        link: '/guide/livewire-component.html',
      },
      { text: 'Performance', link: '/guide/performance.html' },
    ],
    sidebar: {
      '/guide/': [
        {
          text: 'Guide',
          collapsible: false,
          children: [
            '/guide/getting-started.md',
            '/guide/configuration.md',
            '/guide/attributes.md',
            '/guide/livewire-component.md',
            '/guide/performance.md',
            '/guide/testing.md',
          ],
        },
      ],
      '/': [
        {
          text: 'Introduction',
          collapsible: false,
          children: ['/README.md'],
        },
      ],
    },
  }),
  plugins: [
    searchPlugin({
      maxSuggestions: 15,
      hotKeys: ['s', '/'],
      isSearchable: (page) => page.path !== '/',
    }),
  ],
  bundler: viteBundler(),
});

