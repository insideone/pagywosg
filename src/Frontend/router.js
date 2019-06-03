import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router);

import IndexPage from './pages/Index.vue';
import EventsPage from './pages/EventsPage.vue';
import NewEventPage from './pages/NewEventPage.vue';
import EventPage from './pages/EventPage.vue';
import LeaderboardPage from './pages/LeaderboardPage.vue';
import UsersPage from './pages/UsersPage.vue';
import HelpFormattingPage from './pages/HelpFormatting.vue';
import FaqPage from './pages/FaqPage';


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