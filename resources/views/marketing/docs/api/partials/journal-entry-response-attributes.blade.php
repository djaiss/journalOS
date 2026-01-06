<x-marketing.docs.response-attributes>
  <x-marketing.docs.attribute name="type" type="string" description="The type of the resource." />
  <x-marketing.docs.attribute name="id" type="string" description="The ID of the journal entry." />
  <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the journal entry." />
  <x-marketing.docs.attribute name="attributes.journal_id" type="integer" description="The ID of the journal." />
  <x-marketing.docs.attribute name="attributes.day" type="integer" description="The day of the journal entry." />
  <x-marketing.docs.attribute name="attributes.month" type="integer" description="The month of the journal entry." />
  <x-marketing.docs.attribute name="attributes.year" type="integer" description="The year of the journal entry." />
  <x-marketing.docs.attribute name="attributes.modules" type="object" description="The modules included with the journal entry." />
  <x-marketing.docs.attribute name="attributes.modules.sleep" type="object" description="The sleep module payload." />
  <x-marketing.docs.attribute name="attributes.modules.sleep.bedtime" type="string" description="The bedtime time of the journal entry." />
  <x-marketing.docs.attribute name="attributes.modules.sleep.wake_up_time" type="string" description="The wake up time of the journal entry." />
  <x-marketing.docs.attribute name="attributes.modules.sleep.sleep_duration_in_minutes" type="integer" description="The sleep duration in minutes." />
  <x-marketing.docs.attribute name="attributes.modules.work" type="object" description="The work module payload." />
  <x-marketing.docs.attribute name="attributes.modules.work.worked" type="string" description="Whether you worked on the journal entry." />
  <x-marketing.docs.attribute name="attributes.modules.work.work_mode" type="string" description="The work mode for the journal entry." />
  <x-marketing.docs.attribute name="attributes.modules.travel" type="object" description="The travel module payload." />
  <x-marketing.docs.attribute name="attributes.modules.travel.has_traveled_today" type="string" description="Whether you traveled today." />
  <x-marketing.docs.attribute name="attributes.modules.travel.travel_mode" type="array" description="The travel modes used for the journal entry." />
  <x-marketing.docs.attribute name="attributes.modules.kids" type="object" description="The kids module payload." />
  <x-marketing.docs.attribute name="attributes.modules.kids.had_kids_today" type="string" description="Whether you had the kids today." />
  <x-marketing.docs.attribute name="attributes.modules.day_type" type="object" description="The day type module payload." />
  <x-marketing.docs.attribute name="attributes.modules.day_type.day_type" type="string" description="The type of day." />
  <x-marketing.docs.attribute name="attributes.modules.primary_obligation" type="object" description="The primary obligation module payload." />
  <x-marketing.docs.attribute name="attributes.modules.primary_obligation.primary_obligation" type="string" description="What demanded most of your attention." />
  <x-marketing.docs.attribute name="attributes.modules.health" type="object" description="The health module payload." />
  <x-marketing.docs.attribute name="attributes.modules.health.health" type="string" description="How you felt on that day." />
  <x-marketing.docs.attribute name="attributes.modules.mood" type="object" description="The mood module payload." />
  <x-marketing.docs.attribute name="attributes.modules.mood.mood" type="string" description="The mood for that day." />
  <x-marketing.docs.attribute name="attributes.modules.energy" type="object" description="The energy module payload." />
  <x-marketing.docs.attribute name="attributes.modules.energy.energy" type="string" description="The energy level for that day." />
  <x-marketing.docs.attribute name="attributes.modules.social_density" type="object" description="The social density module payload." />
  <x-marketing.docs.attribute name="attributes.modules.social_density.social_density" type="string" description="How crowded the day felt." />
  <x-marketing.docs.attribute name="attributes.modules.physical_activity" type="object" description="The physical activity module payload." />
  <x-marketing.docs.attribute name="attributes.modules.physical_activity.has_done_physical_activity" type="string" description="Whether physical activity was done." />
  <x-marketing.docs.attribute name="attributes.modules.physical_activity.activity_type" type="string" description="The type of physical activity." />
  <x-marketing.docs.attribute name="attributes.modules.physical_activity.activity_intensity" type="string" description="The intensity of the physical activity." />
  <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
  <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
  <x-marketing.docs.attribute name="links" type="object" description="The links to access the journal entry." />
</x-marketing.docs.response-attributes>
