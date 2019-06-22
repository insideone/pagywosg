import BaseApi from './baseApi'
import qs from 'qs';

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/events';
    }

    read(id) {
        return this.axios.get(this.path(id));
    }

    getList(params) {
        return this.axios.get(
            this.path(),
            {
                params,
                paramsSerializer: params => qs.stringify(params)
            }
        );
    }

    create(event) {
        return this.axios.post(this.path(), event);
    }

    update(event) {
        return this.axios.put(this.path(event.id), event);
    }

    delete(eventId) {
        return this.axios.delete(this.path(eventId));
    }

    createEntry(entry) {
        return this.axios.post(this.path(entry.eventId, 'entries'), entry);
    }

    updateEntry(entry) {
        return this.axios.put(this.path(entry.eventId, 'entries', entry.id), entry);
    }

    deleteEntry(entry) {
        return this.axios.delete(this.path(entry.event, 'entries', entry.id));
    }

    verify({id, event, field, value}) {
        return this.axios[value ? 'put' : 'delete'](this.path(event, 'entries', id, 'verified', field));
    }

    getLeaderboard(eventId) {
        return this.axios.get(this.path(eventId, 'leaderboard'));
    }
}
