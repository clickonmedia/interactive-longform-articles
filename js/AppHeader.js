'use strict';

import React from 'react';
import PropTypes from 'prop-types';

const AppHeader = props => {
  const {
    brand,
    permalink,
    title
  } = props;
  return React.createElement("div", {
    className: "interactive-header"
  }, React.createElement("a", {
    href: "/",
    className: "brand"
  }, brand.logo && React.createElement("img", {
    src: brand.logo,
    alt: brand.name
  }), !brand.logo && React.createElement("div", {
    className: "title"
  }, brand.name)), React.createElement("div", {
    className: "social-share"
  }, React.createElement("a", {
    className: "link ia-icon twitter ia-icon-twitter",
    href: `https://twitter.com/intent/tweet?url=${permalink}&text=${title}`,
    target: "_blank"
  }), React.createElement("a", {
    className: "link ia-icon facebook ia-icon-facebook-official",
    href: `https://www.facebook.com/sharer/sharer.php?u=${permalink}`,
    target: "_blank"
  })));
};

AppHeader.propTypes = {
  brand: PropTypes.shape({
    name: PropTypes.string.isRequired,
    logo: PropTypes.string
  }),
  permalink: PropTypes.string.isRequired,
  title: PropTypes.string.isRequired
};
export default AppHeader;