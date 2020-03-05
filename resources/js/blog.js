// 此文件仅用于 blog
require('./bootstrap');
require('./olr/prism.js');
// 需要修改成按需引入
import Element from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(Element, { size: 'small', zIndex: 3000 });

Vue.component('comment-component', require('./components/CommentComponent').default);
const blog = new Vue({
  el: '#blogpage',
});
