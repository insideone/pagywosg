import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router);


const IndexPage = () => import( /* webpackChunkName: "index_page" */ './pages/Index.vue');
const EventsPage = () => import( /* webpackChunkName: "events_page" */ './pages/EventsPage.vue');
const NewEventPage = () => import( /* webpackChunkName: "new_event_page" */ './pages/NewEventPage.vue');
const EventPage = () => import( /* webpackChunkName: "event_detail" */ './pages/EventPage.vue');
const LeaderboardPage = () => import( /* webpackChunkName: "event_leaderboards" */ './pages/LeaderboardPage.vue');
const UsersPage = () => import( /* webpackChunkName: "users_page" */ './pages/UsersPage.vue');
const HelpFormattingPage = () => import( /* webpackChunkName: "help_formatting" */ './pages/HelpFormatting.vue');
const FaqPage = () => import( /* webpackChunkName: "faq_page" */ './pages/FaqPage.vue');


const appRouter = new Router({
    mode: 'history',
    routes: [
        {
            name: 'index',
            path: '/',
            component: IndexPage,
            meta: {title: 'Welcome!'}
        },
        {
            name: 'events',
            path: '/events',
            component: EventsPage,
            meta: {title: 'List of All the Events'}
        },
        {
            name: 'new_event',
            path: '/events/new',
            component: NewEventPage,
            meta: {title: 'Create a New Event'}
        },
        {
            name: 'event_detail',
            path: '/events/:eventId',
            component: EventPage,
            meta: {title: 'Event Details'}
        },
        {
            name: 'event_edit',
            path: '/events/:eventId/edit',
            component: NewEventPage,
            meta: {title: 'Event Details'}
        },
        {
            name: 'event_leaderboard',
            path: '/events/:eventId/leaderboard',
            component: LeaderboardPage,
            meta: {title: 'Event Leaderboard'}
        },
        {
            name: 'users',
            path: '/users',
            component: UsersPage,
            meta: {title: 'Users Manage'}
        },
        {
            name: 'help_formatting',
            path: '/help/formatting',
            component: HelpFormattingPage,
            meta: {title: 'Help: How to Format Texts'}
        },
        {
            name: 'faq',
            path: '/help/faq',
            component: FaqPage,
            meta: {title: 'Help: Frequently Asked Questions'}
        }
    ],
    scrollBehavior: function(to, from, savedPosition){

        if (savedPosition) {
            return savedPosition;
        } else {
            return { x: 0, y: 0 };
        }
    }
});


appRouter.beforeEach((to, from, next) => {
    if (to.meta.title !== undefined)
        document.title = to.meta.title;
    else
        document.title = 'Welcome';

    next();
});

export default appRouter;