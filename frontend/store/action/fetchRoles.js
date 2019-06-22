import api from '../../services/api';

export default ({commit, state}) => {
    return api.roles.getList().then(({data: {roles}}) => {
        let rolesMap = {};

        roles.forEach(role => {
           rolesMap[role.id] = role;
        });

        commit('addRoles', rolesMap);
        return roles;
    });
}
