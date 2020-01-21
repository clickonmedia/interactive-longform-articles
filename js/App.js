'use strict';

import React from 'react';
import ReactDOM from 'react-dom';
import AppHeader from './AppHeader';
import SectionList from './SectionList';
const containerStyles = {
  position: 'absolute',
  top: 0,
  width: '100vw',
  height: '300vh',
  overflowX: 'scroll'
};

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      enabled: true
    };
    this.handleScroll = this.handleScroll.bind(this);
  }

  componentDidMount() {
    console.log('cmd scroll');
    const data = interactive_article_data;
    console.log('section data', data.sections); // Initial first screen

    const sectionIndex = Math.floor(window.scrollY / window.innerHeight);
    console.log('section index', sectionIndex);
    const sections = document.querySelectorAll('.interactive-section');
    console.log('sections', sections);
  } // get document coordinates of the element


  getCoords(elem) {
    const box = elem.getBoundingClientRect();
    return box.top + window.pageYOffset;
  }

  handleScroll(e) {
    console.log('scroll', e);
  }

  render() {
    const data = interactive_article_data;
    const shareURL = window.location.href;
    const shareTitle = document.title;
    console.log('interactive_article_data', interactive_article_data);

    if (!this.state.enabled) {
      return React.createElement("div", null);
    }

    const {
      brand,
      sections
    } = data;
    return React.createElement("div", {
      className: "inner-container",
      style: containerStyles,
      onScroll: this.handleScroll
    }, React.createElement(AppHeader, {
      brand: brand,
      permalink: shareURL,
      title: shareTitle
    }), React.createElement(SectionList, {
      items: sections
    }));
  }

}

const domContainer = document.querySelector('.interactive-article-container');
ReactDOM.render(React.createElement(App), domContainer);