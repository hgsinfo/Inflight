export function getIframeErrorElement() {
  return `
    <div style="
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin-top: 120px;
      font-family: 'Avenir Next W02',Helvetica,Arial,sans-serif;
      font-weight: 400;
      font-size: 14px;
      font-size: 0.875rem;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      font-smoothing: antialiased;
      line-height: 1.5rem;
    "
    id="leadin-iframe-error">
      <img alt="Cannot find page" width="175" src="//static.hsappstatic.net/ui-images/static-1.14/optimized/errors/map.svg">
      <h1 style="
        text-shadow: 0 0 1px transparent;
        margin-bottom: 1.25rem;
        color: #33475b;
        font-size: 1.25rem;
      ">The HubSpot for WordPress plugin is not able to load pages</h1>
      <p>Try disabling your browser extensions and ad blockers, then refresh the page.</p>
      <p>Or open the HubSpot for WordPress plugin in a different browser.</p>
    </div>
  `;
}
