'use strict';

const e = React.createElement;
import SectionList from './SectionList.js';

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      enabled: true
    };
  }

  render() {
    const data = interactive_article_data;
    console.log('interactive_article_data', interactive_article_data);

    if (!this.state.enabled) {
      return React.createElement("div", null);
    }

    return React.createElement("div", null, React.createElement("div", null, "Interactive Longform Articles"), React.createElement(SectionList, {
      items: data.sections
    }));
  }

}

const domContainer = document.querySelector(' #interactive-article-container');
ReactDOM.render(e(App), domContainer);