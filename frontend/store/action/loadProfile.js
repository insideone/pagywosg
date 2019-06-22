import api from '../../services/api';

export default ({commit}) => {
    return api.profile.logged().then(({data: {user, permissions}}) => {
        if (!user) {
            return;
        }

        commit('addUser', user);
        commit('setLoggedUserId', user.id);

        commit('setPermissions', permissions);
    })
}
