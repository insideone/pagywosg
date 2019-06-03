import Vue from 'vue';
import Vuex from 'vuex';

import actions from './action';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        managedUsers: [],
        loggedUserId: 0,
        showedEvents: [],
        events: {},
        users: {},
        eventEntries: {},
        gameCategories: {},
        games: {},
        playStatuses: {},
        roles: {},
        leaderboard: {},
        permissions: {}
    },
    getters: {
        getShowedEvents: (state) => state.showedEvents.map(eventId => state.events[eventId]),

        getManagedUsers: state => state.managedUsers.map(userId => state.users[userId]),

        getEvent: (state) => (eventId) => state.events[eventId],

        getUser: (state) => (userId) => state.users[userId],

        getLoggedUserId: state => state.loggedUserId,

        getLoggedUser: (state) => state.users[ state.loggedUserId ],

        getUsers: (state) => () => state.users,

        getEventEntries: (state) => (idList) => idList.map(id => state.eventEntries[id]),

        getGameCategories: (state) => (idList) => idList.map(id => state.gameCategories[id]),

        getGameCategory: (state) => (catId) => state.gameCategories[catId],

        getGame: (state) => (gameId) => state.games[gameId],

        getPlayStatuses: state => ids => ids === undefined
            ? Object.values(state.playStatuses)
            : ids.map(id => state.playStatuses[id]),

        getPlayStatus: state => id => state.playStatuses[id],

        getRoles: state => ids => ids === undefined
            ? Object.values(state.roles)
            : ids.map(id => state.roles[id]),

        getRole: state => id => state.roles[id],

        getLeaderboard: state => {
            let leaderboard = state.leaderboard;

            if (Object.keys(leaderboard).length === 0)
                return leaderboard;

            leaderboard.sort((a, b) => {
                if (a.hours === b.hours)
                    return 0;
                return (a.hours > b.hours) ? -1 : 1;
            });

            return leaderboard;
        },

        getPermission: state => (permName) => state.permissions[permName],

        isReadingAnyUserAllowed: state => state.permissions['read_any:user'],

        isCreatingEventAllowed: state => state.permissions['create_own:event'],

        isCreatingEntryAllowed: state => state.permissions['create_own:eventEntry'],

        isCreatingHostedEntryAllowed: state => state.permissions['create_hosted:eventEntry'],

        isEditingTheEventAllowed: state => (eventId) => state.permissions['update:event:#'+eventId],

        isEditingTheEntryAllowed: state => (entryId) => state.permissions['update:eventEntry:#'+entryId],

        isDeletingTheEntryAllowed: state => (entryId) => state.permissions['delete:eventEntry:#'+entryId],

        isUpdatingVerificationForTheEntryAllowed: state => (entryId) => state.permissions['update_verification:eventEntry:#'+entryId]

    },
    actions,
    mutations: {
        setShowedEvents: (state, showedEvents) => state.showedEvents = showedEvents,

        setEvents: (state, events) => state.events = events,

        addEvents: (state, events) => state.events = {...state.events, ...events},

        setUsers: (state, users) => state.users = users,

        addUser: (state, user) => state.users = {...state.users, [user.id]: user},

        addUsers: (state, users) => {
            Object.assign(state.users, users);
            return state.users;
        },

        setLoggedUserId: (state, userId) => state.loggedUserId = userId,

        setEventEntries: (state, eventEntries) => state.eventEntries = eventEntries,

        addEventEntries: (state, eventEntries) => state.eventEntries = {...state.eventEntries, ...eventEntries},

        deleteEventEntry: (state, deletedEntry) => {
            delete state.eventEntries[deletedEntry.id];
            // remove link from event
            if (state.events.hasOwnProperty(deletedEntry.event)) {
                let event = state.events[deletedEntry.event];
                event.entries.splice(event.entries.indexOf(deletedEntry.id), 1);
            }
        },

        setGameCategories: (state, gameCategories) => state.gameCategories = gameCategories,

        addGameCategories: (state, gameCategories) => state.gameCategories = {...state.gameCategories, ...gameCategories},

        setGames: (state, games) => state.games = games,

        addGames: (state, games) => state.games = {...state.games, ...games},

        addPlayStatuses: (state, playStatuses) => state.playStatuses = {...state.playStatuses, ...playStatuses},

        addRoles: (state, roles) => state.roles = {...state.roles, ...roles},

        setManagedUsers: (state, users) => state.managedUsers = users,

        addEntryToEvent: (state, {eventId, entryId}) => {
            const entries = state.events[eventId].entries;
            if (entries.indexOf(entryId) === -1) {
                entries.push(entryId);
            }
        },

        setEventEntryFields: (state, {id, fields}) => state.eventEntries[id] = {...state.eventEntries[id], ...fields},

        setLeaderboard: (state, payload) => state.leaderboard = payload,

        setPermissions: (state, payload) => state.permissions = {...state.permissions, ...payload}
    }
});
