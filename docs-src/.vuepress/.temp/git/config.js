import { GitContributors } from "/Users/pshiwanmahmood/code/packages/lararvel-media-selector/node_modules/@vuepress/plugin-git/lib/client/components/GitContributors.js";
import { GitChangelog } from "/Users/pshiwanmahmood/code/packages/lararvel-media-selector/node_modules/@vuepress/plugin-git/lib/client/components/GitChangelog.js";

export default {
  enhance: ({ app }) => {
    app.component("GitContributors", GitContributors);
    app.component("GitChangelog", GitChangelog);
  },
};
