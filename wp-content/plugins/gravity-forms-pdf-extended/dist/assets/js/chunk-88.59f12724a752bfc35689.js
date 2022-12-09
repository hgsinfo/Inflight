"use strict";(self.webpackChunkgravity_pdf=self.webpackChunkgravity_pdf||[]).push([[88],{6088:(e,t,n)=>{n.r(t),n.d(t,{default:()=>p});n(6992),n(3948),n(7658);var a=n(7294),r=n(5697),s=n.n(r);function o(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}
/**
 * Render the button used to option our Fancy PDF template selector
 *
 * @package     Gravity PDF
 * @copyright   Copyright (c) 2022, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       4.1
 */class l extends a.Component{constructor(){super(...arguments),o(this,"handleClick",(e=>{e.preventDefault(),e.stopPropagation(),this.props.history.push("/template")}))}render(){return a.createElement("button",{type:"button",id:"fancy-template-selector",className:"button gfpdf-button",onClick:this.handleClick,ref:e=>this.button=e,"aria-label":GFPDF.manageTemplates},GFPDF.manage)}}o(l,"propTypes",{history:s().object});const p=l}}]);