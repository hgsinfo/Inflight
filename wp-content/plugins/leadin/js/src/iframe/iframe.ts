import { initInterframe } from '../lib/Interframe';
import {
  backgroundIframeUrl,
  impactLink,
  iframeUrl,
} from '../constants/leadinConfig';
import { domElements } from '../constants/selectors';
import Raven from '../lib/Raven';
import { getIframeErrorElement } from './iframeErrorPage';

const IFRAME_DISPLAY_TIMEOUT = 5000;

function getIframeHeight() {
  const adminMenuWrap = document.getElementById('adminmenuwrap');
  const sideMenuHeight = adminMenuWrap ? adminMenuWrap.offsetHeight : 0;
  const adminBar = document.getElementById('wpadminbar');
  const adminBarHeight = (adminBar && adminBar.offsetHeight) || 0;

  if (window.innerHeight < sideMenuHeight) {
    return sideMenuHeight;
  } else {
    return window.innerHeight - adminBarHeight;
  }
}

function addIframeResizeEvent(iframe: HTMLIFrameElement) {
  let animationFrame: number;
  window.addEventListener(
    'resize',
    () => {
      if (animationFrame) {
        cancelAnimationFrame(animationFrame);
      }
      animationFrame = requestAnimationFrame(() => {
        iframe.style.minHeight = `${getIframeHeight()}px`;
      });
    },
    true
  );
}

function createIframeElement(iframeSrc: string) {
  const iframe = document.createElement('iframe');
  iframe.id = 'leadin-iframe';
  iframe.src = iframeSrc;
  iframe.setAttribute('referrerpolicy', 'no-referrer-when-downgrade');
  iframe.setAttribute(
    'sandbox',
    'allow-scripts allow-forms allow-popups allow-top-navigation allow-same-origin'
  );
  iframe.style.minHeight = `${getIframeHeight()}px`;
  addIframeResizeEvent(iframe);
  return iframe;
}

function setupIframeErrorDisplay() {
  const errorString = getIframeErrorElement();

  setTimeout(() => {
    const iframe = document.getElementById(domElements.leadinIframe);
    if (!iframe) {
      const errorHtml = document.createElement('div');
      errorHtml.innerHTML = errorString.trim();

      const container = document.getElementById(
        domElements.leadinIframeContainer
      );
      if (container) {
        container.appendChild(errorHtml);
      }

      Raven.captureException(new Error(`Leadin Iframe blocked`), {
        fingerprint: ['IFRAME_SETUP_ERROR'],
      });
    }
  }, IFRAME_DISPLAY_TIMEOUT);
}

export function createIframe() {
  const link = impactLink
    ? `${impactLink}?u=${encodeURIComponent(`${iframeUrl}`)}&trackConsent=0`
    : iframeUrl;
  const iframe = createIframeElement(link);
  initInterframe(iframe);
  const container = document.getElementById(domElements.leadinIframeContainer);
  if (container) {
    container.appendChild(iframe);
  }
  setupIframeErrorDisplay();
}

export function createBackgroundIframe() {
  const iframe = createIframeElement(backgroundIframeUrl);
  iframe.style.display = 'none';
  initInterframe(iframe);
  document.body.appendChild(iframe);
}
