import { normalize, schema } from 'normalizr';
import userSchema from '../schema/user';
import api from '../../services/api';

export default ({commit, state}, query = null) => {
    return api.users.getList(query).then(({data: result}) => {
        if (!result.hasOwnProperty('users')) {
            return result;
        }

        let users = result.users,
            normalized = normalize(
            {id: 'main', users},
            new schema.Entity('_', {users: [userSchema]})
        );

        let mutations = Object.assign({}, normalized.entities, {
            setManagedUsers: users.map(user => user.id)
        });

        delete mutations._;

        Object.entries(mutations).forEach(
            ([mutation, payload]) => commit(mutation, payload)
        );

        return result;
    });
}
