'use strict';

import React from 'react';

const SectionList = props => {
  const {
    items
  } = props;
  console.log('Section list', items); // const visibility = 'transparent';

  const visibility = '';
  return React.createElement(React.Fragment, null, items.map(item => React.createElement("div", {
    className: `interactive-section ${item.int_section_type} ${item.color} ${visibility}`
  }, React.createElement("div", {
    dangerouslySetInnerHTML: {
      __html: item.progress
    }
  }), React.createElement("div", {
    dangerouslySetInnerHTML: {
      __html: item.background
    }
  }), React.createElement("div", {
    className: "interactive-text"
  }, React.createElement("div", {
    className: "content",
    dangerouslySetInnerHTML: {
      __html: item.content
    }
  })))));
};

export default SectionList;