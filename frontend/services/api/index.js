import axios from 'axios';
import events from './events';
import profile from './profile';
import games from './games';
import playStatuses from './playStatuses';
import users from './users'
import roles from './roles'

Object.assign(axios.defaults, {
    withCredentials: true,
    baseURL: '/'
});

export default {
    events,
    profile,
    games,
    playStatuses,
    users,
    roles
}
