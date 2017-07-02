<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $affectedEntities = $this->getAffectedEntitiesAndData($text);

        return $this->replaceTokens($text, $affectedEntities, $data);
    }

    protected function replaceTokens($text, array $affectedEntities, array $data) {
        $quote = isset($data['quote'])?$data['quote']:null;
        if (isset($affectedEntities['quote']) && $quote instanceof Quote) {
            $text = $this->replaceQuoteRelatedValues($text, $quote);
        }
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();
        $user = isset($data['user'])?$data['user']:$APPLICATION_CONTEXT->getCurrentUser();
        if (isset($affectedEntities['user']) && $user instanceof User) {
            $text = $this->replaceUserRelatedValues($text, $user);
        }

        return $text;
    }

    protected function replaceQuoteRelatedValues($text, Quote $quote) {
        $siteEntity = SiteRepository::getInstance()->getById($quote->siteId);
        $destinationEntity = DestinationRepository::getInstance()->getById($quote->destinationId);

        if ($this->contains('quote', 'summary_html', $text)) {
            $text = str_replace(
                '[quote:summary_html]',
                Quote::renderHtml($quote),
                $text
            );
        }
        if ($this->contains('quote', 'summary', $text)) {
            $text = str_replace(
                '[quote:summary]',
                Quote::renderText($quote),
                $text
            );
        }
        if ($this->contains('quote', 'destination_name', $text)) {
            $text = str_replace('[quote:destination_name]',$destinationEntity->countryName,$text);
        }
        if ($this->contains('quote', 'destination_link', $text)) {
            $destinationLink = '';
            if ($destinationEntity && $siteEntity && $quote) {
                $destinationLink = sprintf(
                    '%s/%s/quote/%s',
                    $siteEntity->url,
                    $destinationEntity->countryName,
                    $quote->id
                );
            }

            $text = str_replace('[quote:destination_link]', $destinationLink, $text);
        }

        return $text;
    }

    protected function replaceUserRelatedValues($text, User $user) {
        if ($this->contains('user', 'first_name', $text)) {
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }

    protected function contains($entity, $value, $text) {
        return false !== strpos($text, sprintf('[%s:%s]', $entity, $value));
    }

    protected function getAffectedEntitiesAndData($text) {
        return [
            'user' => [
                'first_name',
            ],
            'quote' => [
                'destination_name',
                'destination_link',
                'summary',
                'summary_html'
            ]
        ];
    }
}
