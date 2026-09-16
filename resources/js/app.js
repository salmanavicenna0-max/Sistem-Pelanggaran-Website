import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';
import Chart from 'chart.js/auto';

window.Chart = Chart;

// Master Data Components
import SchoolYearManager from './components/master/SchoolYearManager.vue';
import SchoolClassManager from './components/master/SchoolClassManager.vue';
import StudentManager from './components/master/StudentManager.vue';
import TeacherManager from './components/master/TeacherManager.vue';
import ViolationRuleManager from './components/master/ViolationRuleManager.vue';
import AchievementRuleManager from './components/master/AchievementRuleManager.vue';

const app = createApp({});

app.component('example-component', ExampleComponent);
app.component('school-year-manager', SchoolYearManager);
app.component('school-class-manager', SchoolClassManager);
app.component('student-manager', StudentManager);
app.component('teacher-manager', TeacherManager);
app.component('violation-rule-manager', ViolationRuleManager);
app.component('achievement-rule-manager', AchievementRuleManager);

const el = document.getElementById('app');
if (el) {
    app.mount('#app');
}
