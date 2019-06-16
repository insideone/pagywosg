import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/users';
    }

    getList(query = null) {
        return this.axios.get(this.path(), {
            params: {query}
        });
    }

    addRole(user, roleId) {
        return this.axios.put(this.path(user.id, 'roles', roleId));
    }

    removeRole(user, roleId) {
        return this.axios.delete(this.path(user.id, 'roles', roleId));
    }

    read(id) {
        return this.axios.get(this.path(id, 'profile'));
    }
}
