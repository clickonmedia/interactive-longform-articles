'use strict';

import React from 'react';
import styled from 'styled-components';
import { UI, Colors } from '../shared/Config';

const InteractiveSection = styled.section`
	width: 100%;
	min-height: 100vh;
	position: relative;
	transition: opacity 0.6s;

	progress {
		position: fixed;
		top: 0;
		left: 0;
		z-index: 100;
		width: 100%;
		height: 4px;
		opacity: 0.9;
	}

	progress[value] {
		border: none;
		display: block;
		appearance: none;
	}

	progress[value]::-webkit-progress-bar {
		background-color: transparent;
		border-radius: 2px;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25) inset;
	}

	progress[value]::-webkit-progress-value {
		background-size: 35px 20px, 100% 100%, 100% 100%;
		border-radius: 2px;
	}

	progress[value]::-moz-progress-bar {
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25) inset;
		border-radius: 2px;
	}

	.scroll-icon {
		position: fixed;
		bottom: 2px;
		left: 50%;
		font-size: 3vw;
		color: ${Colors.white};
		opacity: 0.9;
		transition: opacity 0.8s ease 0.35s;
		translateX: -50%;

		&:hover {
			opacity: 1;
		}
	}

	// div may be used to create margins after certain elements
	.content div {
		min-height: 10px;
	}

	.content div, p, h1, h2, h6 {
		color: ${Colors.white};
	}

	/*
		Intro styling
	*/
	&.cover {

		h1, h2, p, p.byline {
			max-width: 610px;
		    font-weight: 600;
		    margin: 0 auto;
		    padding: 0 ${UI.margin};
		    width: 100%;
		    text-shadow: 0 0 5px rgba(0,0,0,0.8);
		}

		h1, h2 {
			font-size: 36px;
			line-height: 1.2;
			margin-top: 3rem;
			margin-bottom: 1rem;
		}

		p {
			font-size: 18px;
			line-height: 1.5rem;
			margin-bottom: 1rem;

			&.byline {
				font-weight: 600;
				margin-top: -0.5rem;
				margin-bottom: 0.5rem;
			}
		}
	}

	/*
		Body styling
	*/
	h2, p {
		max-width: 610px;
		margin: 0 auto;
		padding: 0 ${UI.margin};
	}

	h2 {
		font-size: 26px;
		font-weight: 700;
		margin-bottom: 1.25rem;
	}

	p {
		font-size: 1.0625rem;
		line-height: 1.625rem;
		letter-spacing: -.004em;
	    font-weight: 400;
	    margin-bottom: 1.25rem;

	    &.intro-para {
		    font-size: 1.2rem;
		    line-height: 1.5rem;
		    font-weight: 400;
		    letter-spacing: 0;
		    margin-bottom: 1rem;
		}

		&.byline {
			font-size: 0.89em;
			display: block;
		}
	}

	// Blockquote
	blockquote p {
		font-size: 36px;
		font-weight: 700;
		line-height: 1.2;
		width: 100%;
		max-width: 610px;
	    padding: 0 ${UI.margin};
		margin-top: 1rem auto;
		text-shadow: 0 0 5px rgba(0,0,0,0.8);
	}

	/*
		Large styling
	*/
	&.large {

		h2 {
			font-size: 30px;
			line-height: 1.45em;
			font-weight: 700;
			margin-bottom: 1.25rem;
		}

		p {
		    font-size: 1.15rem;
		    line-height: 1.61em;
		    font-weight: 500;
		    letter-spacing: -.003em;

		    &.intro-para {
				font-size: 1.4rem;
				line-height: 31px;
				font-weight: 500;
			    letter-spacing: -.003em;
			}
		}
	}

	/*
		Downloads
	*/
	&.downloads {
	    font-size: 14px;

		h2 {
		    font-size: 24px;
		    font-weight: 700;
			margin-bottom: 30px;
			padding: 0;
		}

		a {
			position: relative;
			top: -2px;
			font-size: 10px;
			color: inherit;
			text-transform: uppercase;
			border-bottom: 1px solid;
		}

		.file {
		    margin-bottom: 11px;
		}
	}

	/*
		Interactive embeds
	*/
	iframe {
		max-width: 100%;
	    padding-top: 0;
		margin-bottom: ${UI.margin};
	}

	.wp-video {
	    margin: 0 auto;
	}
`;

const SectionList = (props) => {
	const { items, currentIndex } = props;

	console.log( 'SectionList', items );

	return (
		<React.Fragment>
			{ items.map( ( item, index ) => (
				<InteractiveSection 
					key={ index } 
					className={`interactive-section ${ item.int_section_type } ${ item.color } ${ index !== currentIndex ? 'transparent' : '' }`}>
					<div dangerouslySetInnerHTML={{ __html: item.progress }} />

					<div 
						className="interactive-background"
						data-bg-xs={ item.background.xs }
						data-bg-sm={ item.background.sm }
						data-bg-md={ item.background.md }
						data-bg-xl={ item.background.xl }
						style={{
							backgroundPosition: item.background.align,
							opacity: item.background.opacity
						}}>
					</div>

					{ item.background.video && 
						<video 
							poster={ item.background.poster } 
							data-src-xs={ item.background.mobile } 
							data-src-md={ item.background.video } 
							data-src-xl={ item.background.video } 
							preload="auto" 
							width="16" 
							height="9" 
							autoplay 
							loop 
							muted>
						</video>
					}

					<div className="interactive-text">
						<div className="content" dangerouslySetInnerHTML={{ __html: item.content }}></div>
					</div>
				</InteractiveSection>
			)) }
		</React.Fragment>
	);
}

export default SectionList