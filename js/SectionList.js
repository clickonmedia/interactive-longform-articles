'use strict';

import _ from 'lodash';
import ErrorBoundary from './ErrorBoundary';

const SectionList = props => {
  const {
    items
  } = props;
  console.log('Section list items', items);

  const item = _.first(items);

  return React.createElement("div", {
    className: `interactive-section ${item.int_section_type} ${item.color} transparent`
  }, React.createElement("div", {
    dangerouslySetInnerHTML: {
      __html: item.progress
    }
  }), ";", React.createElement("div", {
    dangerouslySetInnerHTML: {
      __html: item.background
    }
  }), ";", React.createElement("div", {
    className: "interactive-text"
  }, React.createElement("div", {
    className: "content",
    dangerouslySetInnerHTML: {
      __html: item.content
    }
  })));
};

export default SectionList;