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
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {
            $quoteEntity = QuoteRepository::getInstance()->getById($quote->id);
            $siteEntity = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationEntity = DestinationRepository::getInstance()->getById($quote->destinationId);

            if ($this->contains('quote', 'summary_html', $text)) {
                $text = str_replace(
                    '[quote:summary_html]',
                    Quote::renderHtml($quoteEntity),
                    $text
                );
            }
            if ($this->contains('quote', 'summary', $text)) {
                $text = str_replace(
                    '[quote:summary]',
                    Quote::renderText($quoteEntity),
                    $text
                );
            }
            if ($this->contains('quote', 'destination_name', $text)) {
                $text = str_replace('[quote:destination_name]',$destinationEntity->countryName,$text);
            }
            if ($this->contains('quote', 'destination_link', $text)) {
                $destinationLink = '';
                if ($destinationEntity && $siteEntity && $quoteEntity) {
                    $destinationLink = sprintf(
                        '%s/%s/quote/%s',
                        $siteEntity->url,
                        $destinationEntity->countryName,
                        $quoteEntity->id
                    );
                }
                
                $text = str_replace('[quote:destination_link]', $destinationLink, $text);
            }
        }
        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $APPLICATION_CONTEXT->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);
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
