(window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[]).push([[70,73],{18:function(e,t,n){"use strict";n.d(t,"a",(function(){return c})),n.d(t,"b",(function(){return a}));const c=e=>!(e=>null===e)(e)&&e instanceof Object&&e.constructor===Object;function a(e,t){return c(e)&&t in e}},214:function(e,t,n){"use strict";n.d(t,"a",(function(){return a})),n(100);var c=n(44);const a=()=>c.m>1},215:function(e,t,n){"use strict";n.d(t,"a",(function(){return r}));var c=n(28),a=n(18);const r=e=>Object(c.a)(e)?JSON.parse(e)||{}:Object(a.a)(e)?e:{}},22:function(e,t,n){"use strict";var c=n(0),a=n(5),r=n.n(a);t.a=e=>{let t,{label:n,screenReaderLabel:a,wrapperElement:s,wrapperProps:l={}}=e;const o=null!=n,i=null!=a;return!o&&i?(t=s||"span",l={...l,className:r()(l.className,"screen-reader-text")},Object(c.createElement)(t,l,a)):(t=s||c.Fragment,o&&i&&n!==a?Object(c.createElement)(t,l,Object(c.createElement)("span",{"aria-hidden":"true"},n),Object(c.createElement)("span",{className:"screen-reader-text"},a)):Object(c.createElement)(t,l,n))}},275:function(e,t,n){"use strict";n.d(t,"a",(function(){return l}));var c=n(108),a=n(214),r=n(18),s=n(215);const l=e=>{if(!Object(a.a)())return{className:"",style:{}};const t=Object(r.a)(e)?e:{},n=Object(s.a)(t.style);return Object(c.__experimentalUseBorderProps)({...t,style:n})}},291:function(e,t,n){"use strict";n.d(t,"a",(function(){return l}));var c=n(108),a=n(214),r=n(18),s=n(215);const l=e=>{if(!Object(a.a)())return{className:"",style:{}};const t=Object(r.a)(e)?e:{},n=Object(s.a)(t.style);return Object(c.__experimentalUseColorProps)({...t,style:n})}},299:function(e,t,n){"use strict";n.d(t,"a",(function(){return r}));var c=n(18),a=n(215);const r=e=>{const t=Object(c.a)(e)?e:{},n=Object(a.a)(t.style),r=Object(c.a)(n.typography)?n.typography:{};return{style:{fontSize:t.fontSize?`var(--wp--preset--font-size--${t.fontSize})`:r.fontSize,lineHeight:r.lineHeight,fontWeight:r.fontWeight,textTransform:r.textTransform,fontFamily:t.fontFamily}}}},317:function(e,t,n){"use strict";n.r(t),n.d(t,"Block",(function(){return p}));var c=n(0),a=n(1),r=n(5),s=n.n(r),l=n(22),o=n(47),i=n(275),u=n(291),b=n(299),d=n(324),m=n(132);n(318);const p=e=>{const{className:t,align:n}=e,{parentClassName:r}=Object(o.useInnerBlockLayoutContext)(),{product:m}=Object(o.useProductDataContext)(),p=Object(i.a)(e),f=Object(u.a)(e),O=Object(b.a)(e),j=Object(d.a)(e);if(!m.id||!m.on_sale)return null;const g="string"==typeof n?"wc-block-components-product-sale-badge--align-"+n:"";return Object(c.createElement)("div",{className:s()("wc-block-components-product-sale-badge",t,g,{[r+"__product-onsale"]:r},f.className,p.className),style:{...f.style,...p.style,...O.style,...j.style}},Object(c.createElement)(l.a,{label:Object(a.__)("Sale","woocommerce"),screenReaderLabel:Object(a.__)("Product on sale","woocommerce")}))};t.default=Object(m.withProductDataContext)(p)},318:function(e,t){},324:function(e,t,n){"use strict";n.d(t,"a",(function(){return l}));var c=n(108),a=n(214),r=n(18),s=n(215);const l=e=>{if(!Object(a.a)()||"function"!=typeof c.__experimentalGetSpacingClassesAndStyles)return{style:{}};const t=Object(r.a)(e)?e:{},n=Object(s.a)(t.style);return Object(c.__experimentalGetSpacingClassesAndStyles)({...t,style:n})}},336:function(e,t,n){"use strict";n.d(t,"a",(function(){return j}));var c=n(11),a=n.n(c),r=n(0),s=n(1),l=n(5),o=n.n(l),i=n(2),u=n(47),b=n(299),d=n(275),m=n(324),p=n(132),f=n(66),O=n(317);n(337);const j=e=>{const{className:t,imageSizing:n="full-size",showProductLink:c=!0,showSaleBadge:a,saleBadgeAlign:l="right"}=e,{parentClassName:i}=Object(u.useInnerBlockLayoutContext)(),{product:p,isLoading:j}=Object(u.useProductDataContext)(),{dispatchStoreEvent:h}=Object(f.a)(),w=Object(b.a)(e),_=Object(d.a)(e),k=Object(m.a)(e);if(!p.id)return Object(r.createElement)("div",{className:o()(t,"wc-block-components-product-image",{[i+"__product-image"]:i},_.className),style:{...w.style,..._.style,...k.style}},Object(r.createElement)(g,null));const E=!!p.images.length,S=E?p.images[0]:null,N=c?"a":r.Fragment,v=Object(s.sprintf)(
/* translators: %s is referring to the product name */
Object(s.__)("Link to %s","woocommerce"),p.name),x={href:p.permalink,...!E&&{"aria-label":v},onClick:()=>{h("product-view-link",{product:p})}};return Object(r.createElement)("div",{className:o()(t,"wc-block-components-product-image",{[i+"__product-image"]:i},_.className),style:{...w.style,..._.style,...k.style}},Object(r.createElement)(N,c&&x,!!a&&Object(r.createElement)(O.default,{align:l,product:p}),Object(r.createElement)(y,{fallbackAlt:p.name,image:S,loaded:!j,showFullSize:"cropped"!==n})))},g=()=>Object(r.createElement)("img",{src:i.PLACEHOLDER_IMG_SRC,alt:"",width:500,height:500}),y=e=>{let{image:t,loaded:n,showFullSize:c,fallbackAlt:s}=e;const{thumbnail:l,src:o,srcset:i,sizes:u,alt:b}=t||{},d={alt:b||s,hidden:!n,src:l,...c&&{src:o,srcSet:i,sizes:u}};return Object(r.createElement)(r.Fragment,null,d.src&&Object(r.createElement)("img",a()({"data-testid":"product-image"},d)),!t&&Object(r.createElement)(g,null))};t.b=Object(p.withProductDataContext)(j)},337:function(e,t){},499:function(e,t,n){"use strict";n.r(t);var c=n(132),a=n(336);t.default=Object(c.withFilteredAttributes)({showProductLink:{type:"boolean",default:!0},showSaleBadge:{type:"boolean",default:!0},saleBadgeAlign:{type:"string",default:"right"},imageSizing:{type:"string",default:"full-size"},productId:{type:"number",default:0},isDescendentOfQueryLoop:{type:"boolean",default:!1}})(a.b)}}]);