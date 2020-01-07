<template>

    <div class="event-creation">

        <h1 class="heading">
            <span v-if="isNew">Create New Event</span>
            <span v-else>Update Event: `{{event.name}}`</span>
        </h1>

        <messagebox
            v-if="isLoading"
            :mode="'loading'"
        />

        <messagebox
            v-for="(error, key) in onLoadErrors"
            :key="'new_error_'+key"
            mode="error"
        >{{error.message}}</messagebox>

        <div class="text">
            <p>On this page you can <span v-if="isNew">create an</span><span v-else>update the</span> event</p>
            <!--
            <p>Please, follow these guidlines:</p>
            <ul>
                <li>...</li>
            </ul>
            -->
        </div>

        <form
            v-if="!isLoading"
            @submit.prevent="submitForm"
            action="" method="post" class="form event-creation__form"
        >
            <div class="form__block">
                <label for="e_title" class="form__label">
                    <i class="icon-fa icon-fa--blue icon-fa--inline icon-fa--sized20 fas fa-newspaper"></i>
                    Event Title <span class="form__mark-required" title="This field is required">*</span>
                </label>
                <input
                    v-model="event.name"
                    required
                    type="text" class="form__input"
                    name="title" id="e_title"
                />
            </div>
            <div class="form__block">
                <div class="form__label">
                    <i class="icon-fa icon-fa--blue icon-fa--inline icon-fa--sized20 fas fa-calendar-alt"></i>
                    Time Interval <span class="form__mark-required" title="This field is required">*</span>
                </div>
                <div class="form__note">
                    This is the time period when users can submit and edit their entries. So, the ending date can be set up as a few days later than the&nbsp;"real" ending date of&nbsp;the&nbsp;event.
                </div>
                <div class="form__subblock">
                    <label for="e_date_from" class="form__label"></label>
                    <input
                        v-model="event.startedAt"
                        required
                        type="date" class="form__input" name="date_from" id="e_date_from"
                    />
                </div>
                <div class="form__divider">&mdash;</div>
                <div class="form__subblock">
                    <label for="e_date_to" class="form__label"></label>
                    <input
                        v-model="event.endedAt"
                        required
                        type="date" class="form__input" name="date_to" id="e_date_to"
                    />
                </div>
            </div>
            <div class="form__block">
                <label for="e_descr" class="form__label">
                    <i class="icon-fa icon-fa--blue icon-fa--inline icon-fa--sized20 far fa-file-alt"></i>
                    Event Description
                </label>
                <div class="form__note">
                    All the text goes here. Actually anything can be here, any texts, tables, pictures or SG&nbsp;thread links.
                </div>
                <textarea
                    v-model="event.description"
                    class="form__textarea" name="descr" id="e_descr"
                ></textarea>

                <div
                    v-if="event.description"
                    class="form__preview-block"
                >
                    <div class="form__preview-head">Description Preview:</div>
                    <div class="form__preview-content text" v-html="compiledDescription"></div>
                </div>
            </div>
            <div class="form__block">
                <label for="e_unlocks" class="form__label">
                    <i class="icon-fa icon-fa--blue icon-fa--inline icon-fa--sized20 fas fa-unlock-alt"></i>
                    Event Unlocks
                </label>
                <div class="form__note">
                    Here you can put anything that should be visible only to people who have beaten at least one game, e.g. the&nbsp;rewards for participating in&nbsp;the&nbsp;event, such as SG&nbsp;invite-only giveaway links.
                </div>
                <textarea
                    v-model="event.unlocks"
                    class="form__textarea"
                    name="descr"
                    id="e_unlocks"
                ></textarea>

                <div
                    v-if="event.unlocks"
                    class="form__preview-block"
                >
                    <div class="form__preview-head">Unlocks Preview:</div>
                    <div class="form__preview-content text" v-html="compiledUnlocks"></div>
                </div>
            </div>
            <div class="form__block form__block--half">
                <label class="form__label">
                    <i class="icon-fa icon-fa--blue icon-fa--inline icon-fa--sized20 fas fa-list"></i>
                    Qualified Games Subcategories
                </label>
                <div class="form__note">
                    The subcategories under which the participating games can&nbsp;be qualified. This list will&nbsp;be the&nbsp;one users are selecting their fitting category from. <br /> The new empty field is added automatically when you start typing in&nbsp;the&nbsp;last empty field.
                </div>
                <div
                    v-for="(subCatItem, i) in event.gameCategories"
                    :key="'subcat_'+i"
                    class="form__block form__block--multiple"
                >
                    <input
                        v-model="event.gameCategories[i]"
                        @input="checkSubCategories"
                        :id="'subcat_'+i"
                        type="text"
                        class="form__input"
                        name="subcat[]"
                        placeholder="Add subcategory name..."
                    />
                </div>
            </div>

            <messagebox
                v-for="(error, key) in errors"
                :key="'new_error_'+key"
                mode="error"
            >{{error.message}}</messagebox>

            <div class="form__submit-block">
                <button type="submit" class="button button--submit">
                    <i class="icon-fa icon-fa--inline fas fa-plus-square"></i>
                    {{isNew ? 'Create' : 'Update'}} Event
                </button>
            </div>
        </form>


    </div>

</template>

<script>
    import marked from 'marked';

    import Messagebox from '../components/Messagebox.vue';

    import { mapActions, mapGetters } from 'vuex'

    export default {
        name: "NewEventPage",
        components: {
            Messagebox
        },
        data: function() {
            return {
                isLoading: true,
                errors: [],
                onLoadErrors: [],
                event: {
                    id: '',
                    name: '',
                    startedAt: '',
                    endedAt: '',
                    description: '',
                    unlocks: '',
                    gameCategories: ['', '', '']
                }
            };
        },
        computed: {
            isNew() {
                return !this.eventId;
            },
            eventId() {
                return this.$route.params.eventId;
            },
            compiledDescription() {
                return this.renderMarkdown(this.event.description);
            },
            compiledUnlocks() {
                return this.renderMarkdown(this.event.unlocks);
            },
            urlEventName() {
                return this.$urlify(this.event.name);
            }
        },
        created() {
            if (!this.eventId) {
                this.isLoading = false;
                return;
            }

            this.fetchEvent(this.eventId).then(event => {
                this.isLoading = false;
                this.event = Object.assign({}, event);
                this.event.gameCategories = event.gameCategories.map(gameCategory => gameCategory.name).concat(['']);
            })
                .catch(e => {
                    this.onLoadErrors = this.errors = e.response.data.errors ? e.response.data.errors : [{message: e}];;
                });
        },
        methods: {
            ...mapActions(['sendEvent', 'fetchEvent']),

            renderMarkdown(text) {
                if (!text) {
                    return '';
                }

                return marked(text, {sanitize: true, breaks: true});
            },

            checkSubCategories () {
                if (this.event.gameCategories[ this.event.gameCategories.length - 1 ] !== '') {
                    this.event.gameCategories.push('');
                }
            },

            submitForm() {
                this.sendEvent(this.event).then(({id: eventId}) => {
                    // redirecting to event page
                    this.$router.push({name: 'event_detail_name', params: {eventId, eventName: this.urlEventName}});
                })
                    .catch(e => {
                        this.errors = e.response.data.errors ? e.response.data.errors : [{message: e}];
                    })
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/mixins";

    @import "../assets/blocks/form";

    .event-creation{


        &__form{
            max-width: 800px;
        }
    }


</style>