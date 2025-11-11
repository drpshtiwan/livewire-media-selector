import { CodeTabs } from "/Users/pshiwanmahmood/code/packages/lararvel-media-selector/node_modules/@vuepress/plugin-markdown-tab/lib/client/components/CodeTabs.js";
import { Tabs } from "/Users/pshiwanmahmood/code/packages/lararvel-media-selector/node_modules/@vuepress/plugin-markdown-tab/lib/client/components/Tabs.js";
import "/Users/pshiwanmahmood/code/packages/lararvel-media-selector/node_modules/@vuepress/plugin-markdown-tab/lib/client/styles/vars.css";

export default {
  enhance: ({ app }) => {
    app.component("CodeTabs", CodeTabs);
    app.component("Tabs", Tabs);
  },
};
