<template>
    <AppLayout title="Billing">
        <template #header>
            Billing
        </template>

        <div class="mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Subscription Status -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Subscription Status</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="isTrialActive" class="flex items-center justify-between">
                            <div>
                                <p class="heading-lg text-brand-600">{{ trialTimeRemaining }}</p>
                                <p class="secondary-text">remaining on your free trial</p>
                            </div>
                            <Button @click="goToCheckout" :loading="loading">Upgrade Now</Button>
                        </div>
                        <div v-else-if="isSubscribed" class="flex items-center justify-between">
                            <div>
                                <p class="heading-lg text-brand-600">Active Subscription</p>
                                <p v-if="nextBillingDateFormatted"
                                   class="secondary-text">Your next billing date is {{ nextBillingDateFormatted }}</p>
                            </div>
                            <Button @click="goToBillingPortal" :loading="loading" variant="outline">Manage Subscription</Button>
                        </div>
                        <div v-else class="flex items-center justify-between">
                            <div>
                                <p class="heading-lg text-red-600">No Active Subscription</p>
                                <p class="secondary-text">Subscribe to continue using our services</p>
                            </div>
                            <Button @click="goToCheckout" :loading="loading">Subscribe Now</Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Subscription Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Subscription Details</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="heading-lg text-brand-600 mb-2">$24.99<span class="text-sm text-slate-600 ml-0.5">/ month</span></p>
                        <ul class="space-y-2">
                            <li class="flex items-center">
                                <CheckCircle2 class="w-5 h-5 text-brand-600 mr-2" />
                                <span class="secondary-text">Radar job matching</span>
                            </li>
                            <li class="flex items-center">
                                <CheckCircle2 class="w-5 h-5 text-brand-600 mr-2" />
                                <span class="secondary-text">Prefilled applications</span>
                            </li>
                            <li class="flex items-center">
                                <CheckCircle2 class="w-5 h-5 text-brand-600 mr-2" />
                                <span class="secondary-text">Job database access</span>
                            </li>
                            <li class="flex items-center">
                                <CheckCircle2 class="w-5 h-5 text-brand-600 mr-2" />
                                <span class="secondary-text">Auto-apply functionality</span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <!-- FAQ Section -->
                <Card class="md:col-span-3">
                    <CardHeader>
                        <CardTitle>Frequently Asked Questions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Accordion type="single" collapsible>
                            <AccordionItem v-for="(faq, index) in faqs" :key="index" :value="`item-${index}`">
                                <AccordionTrigger class="heading-sm">{{ faq.question }}</AccordionTrigger>
                                <AccordionContent>
                                    <p class="secondary-text">{{ faq.answer }}</p>
                                </AccordionContent>
                            </AccordionItem>
                        </Accordion>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {Link} from '@inertiajs/vue3';
import {route} from 'ziggy-js';
import AppLayout from "@/Layouts/AppLayout.vue";
import {Button} from '@/components/button';
import {Card, CardContent, CardHeader, CardTitle} from '@/components/card';
import {Accordion, AccordionContent, AccordionItem, AccordionTrigger} from '@/components/accordion';
import {CheckCircle2} from 'lucide-vue-next';
import moment from "moment";

export default defineComponent({
    name: "BillingPortal",
    components: {
        AppLayout,
        Link,
        Button,
        Card,
        CardHeader,
        CardTitle,
        CardContent,
        Accordion,
        AccordionItem,
        AccordionTrigger,
        AccordionContent,
        CheckCircle2,
    },
    props: {
        next_billing_date: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            loading: false,
            faqs: [
                {
                    question: "How does billing work?",
                    answer: "We charge $24.99 monthly for full access to all features. Your subscription will automatically renew each month unless cancelled."
                },
                {
                    question: "Can I cancel my subscription?",
                    answer: "Yes, you can cancel your subscription at any time through the Stripe billing portal. Your access will continue until the end of your current billing period."
                },
                {
                    question: "What happens after my trial ends?",
                    answer: "After your 3-day trial ends, you'll need to subscribe to continue using our services. Don't worry, we'll remind you before your trial expires."
                }
            ]
        };
    },
    computed: {
        trialTimeRemaining() {
            const futureDate = moment(this.$page.props.auth.user.trial_ends_at);
            const now = moment();

            const duration = moment.duration(futureDate.diff(now));

            const days = Math.floor(duration.asDays());
            const hours = duration.hours();

            return `${days} days ${hours} hours`;
        },
        isTrialActive() {
            return this.$page.props.auth.user.on_trial;
        },
        isSubscribed() {
            return this.$page.props.auth.user.subscribed;
        },
        nextBillingDateFormatted() {
            return moment(this.next_billing_date).format('MMMM Do, YYYY');
        }
    },
    methods: {
        goToCheckout() {
            this.loading = true;
            window.location.href = route('billing.checkout');
        },
        goToBillingPortal() {
            this.loading = true;
            window.location.href = this.$page.props.billing_portal_url;
        }
    }
});
</script>
