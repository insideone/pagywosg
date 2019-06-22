import api from '../../services/api';

export default ({commit}, payload) => {
    return api.users[payload.active ? 'addRole' : 'removeRole'](payload.user, payload.roleId).then(({data: {errors}}) => {

    });
}
